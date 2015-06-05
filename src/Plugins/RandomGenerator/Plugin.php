<?php
namespace Slackyboy\Plugins\RandomGenerator;

use Slack\Channel;
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
            if ($message->matchesAll('/random/')) {
                $random = $this->generator->integers(0, 100000, 1);

                $message->getChannel()->then(function (Channel $channel) {
                    $this->bot->say('Here\'s a random number: '.$random[0]."\n(Courtesy of random.org)", $channel);
                });
            }
        });
    }
}
