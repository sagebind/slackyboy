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
            if ($message->matchesAll('/quit/')) {
                $this->bot->say('Goodbye.', $message->getChannel());

                $this->bot->quit();
            }

            if ($message->matchesAll('/restart/')) {
                $this->bot->say('Goodbye. I\'ll be back in a jiffy.', $message->getChannel());

                $this->bot->restart();
            }

            if ($message->matchesAll('/users/')) {
                $users = $this->bot->getSlackClient()->getUsers();

                $text = '';
                foreach ($users as $user) {
                    $text .= '@'.$user->getUsername()."\n";
                }
                $this->bot->say($text, $message->getChannel());
            }

            if ($message->matchesAll('/plugins/i', '/(running|loaded|enabled)/i')) {
                $responseText = 'I have these plugins loaded right now:';

                foreach ($this->getPluginManager()->getPlugins() as $name => $instance) {
                    $responseText .= "\n`".$name.'`';
                }

                $this->bot->say($responseText, $message->getChannel());
            }
        });
    }

    public function userIsAdmin(User $user)
    {
        return false;
    }
}
