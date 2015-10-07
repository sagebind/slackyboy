<?php
namespace Slackyboy\Plugins\AdminControl;

use Slackyboy\Message;
use Slackyboy\Plugins\AbstractPlugin;
use Slack\ChannelInterface;
use Slack\User;

/**
 * Class Plugin
 */
class Plugin extends AbstractPlugin
{
    public function enable()
    {
        // attach event handlers
        $this->bot->on('mention', function (Message $message) {
            if ($message->matchesAll('/quit/')) {
                $message->getChannel()->then(function (ChannelInterface $channel) {
                    $this->bot->say('Goodbye.', $channel);
                    $this->bot->quit();
                });
            }

            if ($message->matchesAll('/restart|reboot/i')) {
                $message->getChannel()->then(function (ChannelInterface $channel) {
                    $this->bot->say('I am rebooting my systems. I\'ll be back momentarily.', $channel);
                    $this->bot->restart();
                });
            }

            if ($message->matchesAll('/users/i')) {
                $this->bot->getSlackClient()->getUsers()->then(function ($users) use ($message) {
                    $text = '';
                    foreach ($users as $user) {
                        $text .= '@' . $user->getUsername() . "\n";
                    }

                    return $message->getChannel()->then(function (ChannelInterface $channel) use ($text) {
                        $this->bot->say($text, $channel);
                    });
                });
            }

            if ($message->matchesAll('/plugins/i', '/(running|loaded|enabled)/i')) {
                $responseText = 'I have these plugins loaded right now:';

                foreach ($this->getPluginManager()->getPlugins() as $name => $instance) {
                    $responseText .= "\n`" . $name . '`';
                }

                $message->getChannel()->then(function (ChannelInterface $channel) use ($responseText) {
                    $this->bot->say($responseText, $channel);
                });
            }

            if ($message->matchesAll('/stats|statistics/i')) {
                $message->getChannel()->then(function (ChannelInterface $channel) {
                    $this->showStats($channel);
                });
            }
        });
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function userIsAdmin(User $user)
    {
        return false;
    }

    /**
     * @param ChannelInterface $channel
     */
    public function showStats(ChannelInterface $channel)
    {
        $startTime = new \DateTime();
        $startTime->setTimestamp($_SERVER['REQUEST_TIME']);
        $uptime = $startTime->diff(new \DateTime('now'));

        $text = sprintf("Bot uptime: %s\nCurrent memory usage: %s\nPeak memory usage: %s\nPHP version: %s",
            $uptime->format('%d days, %h hours, %i minutes, %s seconds'),
            $this->formatBytes(memory_get_usage()),
            $this->formatBytes(memory_get_peak_usage()),
            phpversion()
        );
        $this->bot->say($text, $channel);
    }

    /**
     * Formats a number of bytes to a user-friendly string.
     * Chris Jester-Young's implementation.
     *
     * @param int $size      The size in bytes.
     * @param int $precision The precision level.
     *
     * @return string A formatted string describing the size.
     */
    protected function formatBytes($size, $precision = 2)
    {
        $base     = log($size, 1024);
        $suffixes = ['', 'k', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[(int) $base] . 'B';
    }
}
