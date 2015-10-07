<?php namespace Slackyboy\Plugins;

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

    /**
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Dynamically loads a plugin by name.
     *
     * @param string $className Fully qualified plugin class name
     * @param array  $options   Options
     *
     * @throws \Exception
     */
    public function load($className, $options)
    {
        if (!class_exists($className)) {
            throw new \Exception('The plugin class "' . $className . '" could not be found.');
        }

        // make sure the class implements the plugin interface
        if (!in_array(PluginInterface::class, class_implements($className))) {
            throw new \Exception('The class "' . $className . '" is not a plugin.');
        }

        try {
            /** @var PluginInterface $instance */
            $instance = new $className($this->bot, $this);

            foreach ($options as $key => $value) {
                if (property_exists($instance, $key)) {
                    $instance->$key = $value;
                } else {
                    throw new \Exception('Unknown plugin parameter: ' . $key);
                }
            }

            $instance->enable();
            $this->plugins[$className] = $instance;

            $this->bot->getApplication()->getLogger()->info('Plugin initialized: ' . $className);
        } catch (\Exception $exception) {
            throw new \Exception('Error in loading plugin "' . $className . '": ' . $exception->getMessage());
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
