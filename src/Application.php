<?php
namespace Slackyboy;

class Application
{
    const VERSION = '@VERSION';

    public function run()
    {
        $options = getopt('h', ['help']);

        if (isset($options['h']) || isset($options['help'])) {
            $this->showHelp();
            exit(0);
        }

        $bot = new Bot();
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

EOD;
    }
}
