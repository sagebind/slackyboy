<?php
namespace Slackyboy;

use Evenement\EventEmitterTrait;
use React\EventLoop;
use React\EventLoop\LoopInterface;
use Slack\ChannelInterface;
use Slack\Payload;
use Slack\RealTimeClient;
use Slack\User;
use Slackyboy\Plugins\PluginManager;

/**
 * Main bot object that connects to Slack and emits useful bot-wide events.
 * Class Bot
 */
class Bot
{
    use EventEmitterTrait;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var RealTimeClient A Slack real-time messaging client.
     */
    protected $client;

    /**
     * @var PluginManager The bot-wide plugin manager.
     */
    protected $pluginManager;

    /**
     * @var User The user the bot is running as.
     */
    protected $botUser;

    /**
     * @var LoopInterface A React event loop.
     */
    protected $loop;

    /**
     * Creates a new bot instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->loadPlugins();
        $this->initLoop();
        $this->initClient();
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
     * Runs the bot.
     */
    public function run()
    {
        $this->client->on('message', function (Payload $data) {
            $message = new Message($this->client, $data->getData());

            $this->app->getLogger()->info('Noticed message', [
                'text' => $message->getText(),
            ]);

            $this->emit('message', [$message]);

            if ($message->matchesAny('/' . $this->botUser->getUsername() . '/i')) {
                $this->app->getLogger()->debug('Mentioned in message', [$message]);
                $this->emit('mention', [$message]);
            }
        });

        $this->client->connect()->then(function () {
            return $this->client->getAuthedUser();
        })->then(function (User $user) {
            $this->botUser = $user;
            $this->app->getLogger()->info('Bot user name is configured as ' . $user->getUsername());
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
        $this->app->getLogger()->info('Sending new message');
        $this->client->send($text, $channel);
    }

    /**
     * Quits the bot.
     */
    public function quit()
    {
        $this->app->getLogger()->info('Quitting now');
        $this->client->disconnect();
    }

    /**
     * Restarts the bot process.
     */
    public function restart()
    {
        $this->quit();
        $this->app->getLogger()->info('Restarting now');

        global $argv;
        if (!pcntl_fork()) {
            // We only care about the child fork
            pcntl_exec($argv[0], array_slice($argv, 1));
        }
    }

    /**
     * Loads all plugins specified in configuration.
     */
    protected function loadPlugins()
    {
        // create plugin manager and load plugins
        $this->pluginManager = new PluginManager($this);

        if ($plugins = $this->app->getConfig()->get('plugins')) {
            foreach ($plugins as $name => $options) {
                $this->pluginManager->load($name, $options);
            }
        }
    }

    /**
     * Initialize React event loop
     */
    protected function initLoop()
    {
        $this->loop = EventLoop\Factory::create();
    }

    /**
     * Initialize Slack API client
     */
    protected function initClient()
    {
        $this->client = new RealTimeClient($this->loop);

        if ($token = $this->app->getConfig()->get('slack.token')) {
            $this->client->setToken($token);
        } else {
            throw new \Exception('Specify slack token in configuration file');
        }
    }
}
