<?php namespace Slackyboy\Plugins\Greetings;

use Slack\User;
use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;

/**
 * Class Plugin
 */
class Plugin extends AbstractPlugin
{
    /**
     * @var string
     */
    public $greeting = 'Greetings';

    public function enable()
    {
        $this->bot->on('mention', function (Message $message) {
            if ($message->matchesAny('/hello/i', '/greetings/i')) {
                $message->getChannel()->then(function ($channel) use ($message) {
                    $message->getUser()->then(function (User $user) use ($channel) {
                        $greetings = sprintf('%s, %s!', $this->greeting, $user->getUsername());
                        $this->bot->say($greetings, $channel);
                    });
                });
            }
        });
    }
}
