<?php
namespace Slackyboy\Plugins\Human;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    public function enable()
    {
        $this->bot->on('mention', function (Message $message) {
            if ($message->matchesAny('/thanks/i', '/thank you/i')) {
                $this->bot->say('You\'re welcome.', $message->getChannel());
            }

            // asked to greet someone
            if ($message->matchesAll('/(say (hi|hello|howdy))|greet/i')) {
                if ($message->matchesAll('/to\s+(the\s+)?\w+/i')) {
                    // get the name to greet
                    preg_match('/to\s+(the\s+)?(\w+)/i', $message->getText(), $matches);

                    $name = count($matches) > 2 ? $matches[2] : $matches[1];
                    $this->bot->say('Hello, '.$name.'.', $message->getChannel());
                } else {
                    $this->bot->say('Hello.', $message->getChannel());
                }
            }
        });
    }
}
