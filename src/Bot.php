<?php
namespace Slackyboy;

use Evenement\EventEmitterTrait;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use React\EventLoop;
use React\EventLoop\LoopInterface;
use Slack\ChannelInterface;
use Slack\Payload;
use Slack\RealTimeClient;
use Slack\User;

/**
 * Main bot object that connects to Slack and emits useful bot-wide events.
 *
 * Class Bot
 */
class Bot
{
    use EventEmitterTrait;

    /**
     * @var Config A configuration object.
     */
    protected $config;

    /**
     * @var RealTimeClient A Slack real-time messaging client.
     */
    protected $client;

    /**
     * @var Plugins\PluginManager The bot-wide plugin manager.
     */
    protected $plugins;

    /**
     * @var User The user the bot is running as.
     */
    protected $botUser;

    /**
     * @var LoopInterface A React event loop.
     */
    protected $loop;

    /**
     * @var Logger A logger for all bot-related logs.
     */
    protected $log;

    /**
     * Creates a new bot instance.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        // store config
        $this->config = $config;

        // create a bot-wide log
        $this->log = new Logger('bot');

        // configure the log to write to the config-specified location
        $this->log->pushHandler(new StreamHandler($this->config->get('log'), Logger::DEBUG));

        // load plugins
        $this->loadPlugins();

        $this->loop = EventLoop\Factory::create();

        // create an api client
        $this->client = new RealTimeClient($this->loop);
        $this->client->setToken($this->config->get('slack.token'));
    }

    /**
     * Gets the bot logger.
     *
     * @return Logger
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Gets the bot configuration.
     *
     * @return Config The bot configuration.
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Gets the Slack client instance being used.
     *
     * @return RealTimeClient An active Slack client.
     */
    public function getSlackClient()
    {
        return $this->client;
    }

    /**
     * Loads all plugins specified in configuration.
     */
    public function loadPlugins()
    {
        // create plugin manager and load plugins
        $this->plugins = new Plugins\PluginManager($this);

        foreach ($this->config->get('plugins') as $name => $options) {
            $this->plugins->load($name);
        }
    }

    /**
     * Runs the bot.
     */
    public function run()
    {
        $this->client->on('message', function (Payload $data) {
            $message = new Message($this->client, $data->getData());

            $this->log->info('Noticed message', [
                'text' => $message->getText(),
            ]);

            $this->emit('message', [$message]);

            if ($message->matchesAny('/'.$this->botUser->getUsername().'/i')) {
                $this->log->debug('Mentioned in message', [$message]);
                $this->emit('mention', [$message]);
            }
        });

        $this->client->connect()->then(function () {
            return $this->client->getAuthedUser();
        })->then(function (User $user) {
            $this->botUser = $user;
            $this->log->info('Bot user name is configured as '.$user->getUsername());
        });

        $this->loop->run();
    }

    /**
     * Sends a message to a channel.
     *
     * @param string           $text    The message text to send.
     * @param ChannelInterface $channel The channel to send the message to.
     */
    public function say($text, ChannelInterface $channel)
    {
        $this->log->info('Sending new message');
        $this->client->send($text, $channel);
    }

    /**
     * Quits the bot.
     */
    public function quit()
    {
        $this->log->info('Quitting now');
        $this->client->disconnect();
    }

    /**
     * Restarts the bot process.
     */
    public function restart()
    {
        $this->quit();
        $this->log->info('Restarting now');

        global $argv;
        if (!pcntl_fork()) {
            // We only care about the child fork
            pcntl_exec($argv[0], array_slice($argv, 1));
        }
    }
}
