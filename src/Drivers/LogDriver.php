<?php
namespace KDuma\SMS\Drivers;

class LogDriver implements SMSDriverInterface
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Logging Level
     */
    protected $level;

    /**
     * LogDriver constructor.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param $level
     */
    public function __construct($app, $level)
    {
        $this->app = $app;
        $this->level = $level;
    }

    /**
     * @param $to      string   Recipient phone number
     * @param $message string   Message to send
     *
     * @return void
     */
    public function send($to, $message)
    {
        $this->app->make('log')->log($this->level, 'SMS Messange was sent to '.$to.' with contents: '.$message);
    }
}