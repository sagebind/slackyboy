<?php
namespace Slackyboy;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;

/**
 * Class Application
 */
class Application
{
    const VERSION = '@VERSION';

    public function run()
    {
        $options = getopt('hc:', ['help', 'config:']);

        if (isset($options['h']) || isset($options['help'])) {
            $this->showHelp();
            exit(0);
        }

        $config = $this->getConfig($options);
        $logger = $this->getLogger($config);

        $bot = new Bot($config, $logger);
        $bot->run();
    }

    public function showHelp()
    {
        echo <<< EOD
Slackyboy - Slack chat bot

Usage:
  slackyboy [options]

Options:
  -h, --help    Shows this help message
  -c, --config  Set configuration file (default: slackyboy.json)

EOD;
    }

    /**
     * @param array $options
     *
     * @return Config
     */
    private function getConfig($options)
    {
        if (isset($options['c']) || isset($options['config'])) {
            $file = isset($options['c']) ? $options['c'] : $options['config'];
        } else {
            $file = dirname(__DIR__) . '/slackyboy.json';
        }

        try {
            $config = new Config($file);
        } catch (\Exception $exception) {
            echo 'Config file was not found. Use -c option to specify config path or provide slackyboy.json root file.', PHP_EOL;
            exit(1);
        }

        return $config;
    }

    /**
     * @param Config $config
     *
     * @return Logger
     */
    private function getLogger(Config $config)
    {
        // create a bot-wide log
        $log = new Logger('bot');

        if ($config->get('log')) {
            // configure the log to write to the config-specified location
            $log->pushHandler(new StreamHandler($config->get('log'), Logger::DEBUG));
        }

        return $log;
    }
}
