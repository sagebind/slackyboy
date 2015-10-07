<?php namespace Slackyboy;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noodlehaus\Config;
use Noodlehaus\Exception\FileNotFoundException;

/**
 * Class Application
 */
class Application
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    public function getConfig()
    {
        return $this->config;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function run()
    {
        $options = getopt('hc:', ['help', 'config:']);

        if (isset($options['h']) || isset($options['help'])) {
            $this->showHelp();
            exit(0);
        }

        try {
            $this->initConfig($options);
            $this->initLogger();

            $bot = new Bot($this);
            $bot->run();
        } catch (FileNotFoundException $exception) {
            $this->error(1, 'Config file was not found. Use -c option to specify config path or provide slackyboy.json root file.');
        } catch (\Exception $exception) {
            $this->error(2, $exception->getMessage());
        }
    }

    /**
     * Output error and exit with code
     *
     * @param $code
     * @param $message
     */
    protected function error($code, $message)
    {
        // make sure message have trailing \n
        if (mb_substr($message, -1) !== PHP_EOL) {
            $message .= PHP_EOL;
        }

        $stream = fopen('php://stderr', 'a');
        fwrite($stream, $message);
        fclose($stream);

        exit($code);
    }

    protected function showHelp()
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
     */
    protected function initConfig($options)
    {
        if (isset($options['c']) || isset($options['config'])) {
            $file = isset($options['c']) ? $options['c'] : $options['config'];
        } else {
            $file = dirname(__DIR__) . '/slackyboy.json';
        }

        $this->config = new Config($file);
    }

    protected function initLogger()
    {
        $this->logger = new Logger('slackyboy');

        if ($file = $this->config->get('log')) {
            $this->logger->pushHandler(new StreamHandler($file, Logger::DEBUG));
        }
    }
}
