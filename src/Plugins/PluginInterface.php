<?php
namespace Slackyboy\Plugins;

use Slackyboy\Bot;

interface PluginInterface
{
    public function __construct(Bot $bot, PluginManager $plugins);

    public function enable();
}
