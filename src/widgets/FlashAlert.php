<?php

namespace yiithings\setting\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class FlashAlert extends Widget
{
    /**
     * @var string
     */
    public $sessionKey = 'alerts';
    /**
     * @var array
     */
    public $options = [
        'class' => 'alert alert-dismissible',
        'role' => 'alert',
    ];
    /**
     * @var string
     */
    public $template = '{btn}{message}';
    /**
     * @var string
     */
    public $btnContent = '<span aria-hidden="true">×</span>';
    /**
     * @var array
     */
    public $btnOptions = [
        'class' => 'close',
        'data' => ['dismiss' => 'alert'],
    ];

    /**
     * @return string
     */
    public function run()
    {
        if ( ! Yii::$app->session->hasFlash($this->sessionKey)
            || empty($alerts = Yii::$app->session->getFlash($this->sessionKey))) {
            return '';
        }

        $content = '';
        foreach ($alerts as $alter) {
            $content .= $this->renderAlert($alter);
        }

        return $content;
    }

    /**
     * @param string|AlertMessage $alter
     * @return string
     */
    public function renderAlert($alter)
    {
        if (is_string($alter)) {
            $alter = AlertMessage::message($alter);
        }
        $btn = Html::button($this->btnContent, $this->btnOptions);
        $content = strtr($this->template, [
            '{btn}'     => $btn,
            '{message}' => $alter->message,
        ]);
        Html::addCssClass($this->options, 'alert-' . $alter->level);

        return Html::tag('div', $content, $this->options);
    }
}