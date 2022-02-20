<?php

namespace SlackUnfurl\Traits;

trait SlackEscapeTrait
{
    /**
     * Escape text string.
     * For url use urlencode() as url needs '|'-separator being encoded differently.
     *
     * @param string $text
     * @return string
     * @see https://api.slack.com/docs/message-formatting#how_to_escape_characters
     */
    public function escape(string $text): string
    {
        $replacements = [
            '&' => '&amp;',
            '<' => '&lt;',
            '>' => '&gt;',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Escape url to be safe to be inserted into <URL|text> block.
     * NOTE: does not handle double escaping (%xx already encoded).
     *
     * @param string $url
     * @return string
     * @see https://api.slack.com/docs/message-formatting#how_to_escape_characters
     */
    public function urlencode(string $url): string
    {
        $replacements = [
            '%' => '%25',
            '&' => '%26',
            '<' => '%3C',
            '>' => '%3E',
            '|' => '%7C',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $url);
    }

    /**
     * Create Slack link.
     *
     * This performs url and title escaping properly.
     *
     * @param string $url
     * @param string $title
     * @return string
     */
    public function createLink(string $url, string $title): string
    {
        return sprintf('<%s|%s>', $this->urlencode($url), $this->escape($title));
    }
}
