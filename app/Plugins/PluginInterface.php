<?php namespace Slackyboy\Plugins;

use Slackyboy\Bot;

/**
 * Interface PluginInterface
 */
interface PluginInterface
{
    /**
     * @param Bot           $bot
     * @param PluginManager $plugins
     */
    public function __construct(Bot $bot, PluginManager $plugins);

    public function enable();
}
