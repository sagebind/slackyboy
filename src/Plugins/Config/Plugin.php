<?php
namespace Slackyboy\Plugins\Config;

use Slack\Channel;
use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    public function enable()
    {
        // attach event handlers
        $this->bot->on('message', function (Message $message) {
            if ($message->matchesAll('/config/')) {
                $message->getChannel()->then(function (Channel $channel) {
                    $this->bot->say('What\'s that? Configuration?', $channel);
                });
            }
        });
    }
}
