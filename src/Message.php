<?php
namespace Slackyboy;

class Message extends \Slack\Message
{
    public function isDirect()
    {
    }

    /**
     * Checks if the message text matches all of the given regular expressions.
     *
     * Accepts 1 or more arguments.
     *
     * @param   $pattern1 A regular expression to match against.
     * @param   $pattern2 Another regular expression to match against.
     *
     * @return bool True if the message text matches all of the given regular expressions.
     */
    public function matchesAll($pattern1, $pattern2 = null)
    {
        for ($i = 0; $i < func_num_args(); $i++) {
            if (preg_match(func_get_arg($i), $this->getText()) !== 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the message text matches any of the given regular expressions.
     *
     * Accepts 1 or more arguments.
     *
     * @param   $pattern1 A regular expression to match against.
     * @param   $pattern2 Another regular expression to match against.
     *
     * @return bool True if the message text matches any of the given regular expressions.
     */
    public function matchesAny($pattern1, $pattern2 = null)
    {
        for ($i = 0; $i < func_num_args(); $i++) {
            if (preg_match(func_get_arg($i), $this->getText()) === 1) {
                return true;
            }
        }

        return false;
    }
}
