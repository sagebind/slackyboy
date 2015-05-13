<?php
namespace Slackyboy;

class Message extends Slack\Message
{
    public function isDirect()
    {
    }

    public function matches($patterns)
    {
        foreach (func_get_args() as $pattern) {
            if (preg_match($pattern, $this->text) === 1) {
                return true;
            }
        }

        return false;
    }
}
