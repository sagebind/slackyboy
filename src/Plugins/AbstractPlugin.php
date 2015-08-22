<?php
namespace Slackyboy\Plugins;

use Slackyboy\Bot;

/**
 * Class AbstractPlugin
 */
abstract class AbstractPlugin implements PluginInterface
{
    protected $bot;

    /**
     * @param Bot $bot
     * @param PluginManager $plugins
     */
    public function __construct(Bot $bot, PluginManager $plugins)
    {
        $this->bot = $bot;
        $this->plugins = $plugins;
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager()
    {
        return $this->plugins;
    }
}
