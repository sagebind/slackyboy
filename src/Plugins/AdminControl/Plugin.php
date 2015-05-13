<?php
namespace Slackyboy\Plugins\AdminControl;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;
use Slackyboy\Slack\User;

class Plugin extends AbstractPlugin
{
    public function enable()
    {
        // attach event handlers
        $this->bot->on('mention', function (Message $message) {
            if ($message->matches('/quit/')) {
                $this->bot->say('Goodbye.', $message->getChannel());

                $this->bot->quit();
            }

            if ($message->matches('/users/')) {
                $users = $this->bot->getSlackClient()->getUsers();

                $text = '';
                foreach ($users as $user) {
                    $text .= '@'.$user->getUsername()."\n";
                }
                $this->bot->say($text, $message->getChannel());
            }
        });
    }

    public function userIsAdmin(User $user)
    {
        return false;
    }
}
