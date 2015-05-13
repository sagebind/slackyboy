<?php
namespace Slackyboy\Plugins\Human;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    public function enable()
    {
        $this->bot->on('mention', function (Message $message) {
            if ($message->matches('/thanks/i', '/thank you/i')) {
                $this->bot->say('You\'re welcome.', $message->getChannel());
            }
        });
    }
}
