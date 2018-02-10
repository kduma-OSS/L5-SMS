<?php
namespace KDuma\SMS\NotificationChannel;

class SMSMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * The SMS channel to use.
     *
     * @var string
     */
    public $channel = null;

    /**
     * Create a new message instance.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Create a new message instance.
     *
     * @param string $content
     *
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * Set the message content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $channel
     *
     * @return $this
     */
    public function channel($channel)
    {
        $this->channel = $channel;

        return $this;
    }
}