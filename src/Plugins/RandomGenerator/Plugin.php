<?php
namespace Slackyboy\Plugins\RandomGenerator;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;
use Pixeloution\Random\Randomizer;

class Plugin extends AbstractPlugin
{
    protected $generator;

    public function enable()
    {
        $this->generator = new Randomizer('me@stephencoakley.com');

        // attach event handlers
        $this->bot->on('message', function (Message $message) {
            if ($message->matches('/random/')) {
                $random = $this->generator->integers(0, 100000, 1);

                $this->bot->say('Here\'s a random number: '.$random[0], $message->getChannel());
                $this->bot->say('(Courtesy of random.org)', $message->getChannel());
            }
        });
    }
}
