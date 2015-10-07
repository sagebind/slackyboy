<?php namespace Slackyboy\Plugins;

use Slackyboy\Bot;

/**
 * Class AbstractPlugin
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var Bot
     */
    protected $bot;

    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * @param Bot           $bot
     * @param PluginManager $pluginManager
     */
    public function __construct(Bot $bot, PluginManager $pluginManager)
    {
        $this->bot           = $bot;
        $this->pluginManager = $pluginManager;
    }

    /**
     * @return PluginManager
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }
}
