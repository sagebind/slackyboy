<?php
namespace Slackyboy\Plugins\Config;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    public function enable()
    {
        // attach event handlers
        $this->bot->on('message', function (Message $message) {
            if ($message->matches('/config/')) {
                $this->bot->say('What\'s that? Configuration?', $message->getChannel());
            }
        });
    }
}
