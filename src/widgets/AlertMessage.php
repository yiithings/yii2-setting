<?php

namespace yiithings\setting\widgets;

class AlertMessage
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';
    /**
     * @var string
     */
    public $message;
    /**
     * @var string
     */
    public $level;

    public function __construct($message, $level = self::INFO)
    {
        $this->message = $message;
        $this->level = $level;
    }

    public static function message($message, $level = self::INFO)
    {
        return new static($message, $level);
    }
}