<?php
namespace Slackyboy\Plugins\RandomGenerator;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;
use Pixeloution\Random\Randomizer;

/**
 * Class Plugin
 */
class Plugin extends AbstractPlugin
{
    /**
     * @var Randomizer
     */
    protected $generator;

    public function enable()
    {
        $this->generator = new Randomizer('me@stephencoakley.com');

        // attach event handlers
        $this->bot->on('message', function (Message $message) {
            if ($message->matchesAll('/random/')) {
                $random = $this->generator->integers(0, 100000, 1);

                $message->getChannel()->then(function ($channel) use($random) {
                    $this->bot->say('Here\'s a random number: '.$random[0]."\n(Courtesy of random.org)", $channel);
                });
            }
        });
    }
}
