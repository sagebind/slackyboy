<?php
namespace Slackyboy\Plugins;

use Slackyboy\Bot;

abstract class AbstractPlugin implements PluginInterface
{
    protected $bot;

    public function __construct(Bot $bot, PluginManager $plugins)
    {
        $this->bot = $bot;
        $this->plugins = $plugins;
    }

    public function getPluginManager()
    {
        return $this->plugins;
    }
}
