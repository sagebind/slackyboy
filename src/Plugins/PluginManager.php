<?php
namespace Slackyboy\Plugins;

use Slackyboy\Bot;

/**
 * A plugin manager that manages bot plugin instances. Supports dynamic plugin
 * loading, enabling, disabling, and configuration.
 */
class PluginManager
{
    /**
     * @var PluginInterface[] A map of plugin names to instances.
     */
    protected $plugins;

    /**
     * @var Bot
     */
    protected $bot;

    /**
     * Creates a new plugin manager instance.
     *
     * @param Bot $bot The bot object.
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    public function getPlugin($name)
    {
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Dynamically loads a plugin by name.
     *
     * @param string $className
     * @throws \Exception
     */
    public function load($className)
    {
        if (!class_exists($className)) {
            throw new \Exception('The plugin class "'.$className.'" could not be found.');
        }

        // make sure the class implements the plugin interface
        if (!in_array(PluginInterface::class, class_implements($className))) {
            throw new \Exception('The class "'.$className.'" is not a plugin.');
        }

        try {
            /** @var PluginInterface $instance */
            $instance = new $className($this->bot, $this);
            $instance->enable();
            $this->plugins[$className] = $instance;
        } catch (\Exception $exception) {
            throw new \Exception('Error in loading plugin "'.$className.'"');
        }
    }

    /**
     * Enables all loaded plugins.
     */
    public function enableAll()
    {
        foreach ($this->plugins as $plugin) {
            $plugin->enable();
        }
    }
}
