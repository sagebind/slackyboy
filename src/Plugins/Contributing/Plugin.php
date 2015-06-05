<?php
namespace Slackyboy\Plugins\Contributing;

use Slack\Channel;
use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    public function enable()
    {
        // attach event handlers
        $this->bot->on('mention', function (Message $message) {
            if ($message->matchesAny('/how/i', '/(help|contribute)/i', '/(to\s+you)|(your\s+code)/i')) {
                $message->getChannel()->then(function (Channel $channel) {
                    $this->bot->say('http://github.com/coderstephen/slackyboy', $channel);
                });
            }
        });
    }
}
