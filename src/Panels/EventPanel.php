<?php

namespace Yiisoft\Yii\Debug\Panels;

use Psr\Http\Message\RequestInterface;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays information about triggered events.
 *
 * > Note: this panel requires Yii framework version >= 2.0.14 to function and will not
 *   appear at lower version.
 */
class EventPanel extends Panel
{
    /**
     * @var array current request events
     */
    private $_events = [];
    /** @var \Psr\Http\Message\RequestInterface */
    private $request;
    public function __construct(RequestInterface $request, View $view)
    {
        $this->request = $request;
        parent::__construct($view);
        Event::on('*', '*', function (Event $event) {
            $target = $event->getTarget();
            /* @var $event Event */
            $eventData = [
                'time' => microtime(true),
                'name' => $event->name,
                '__class' => get_class($event),
                'isStatic' => is_object($target) ? '0' : '1',
                'senderClass' => is_object($target) ? get_class($target) : $event->sender,
            ];

            $this->_events[] = $eventData;
        });
    }
    public function getName(): string
    {
        return 'Events';
    }
    public function getSummary(): string
    {
        return $this->render('panels/event/summary', [
            'panel' => $this,
            'eventCount' => count($this->data),
        ]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/event/detail', [
            'panel' => $this,
        ]);
    }
    public function save()
    {
        return $this->_events;
    }
    public function isEnabled(): bool
    {
        $yiiVersion = Yii::getVersion();
        if (!version_compare($yiiVersion, '2.0.14', '>=') && strpos($yiiVersion, '-dev') === false) {
            return false;
        }

        return parent::isEnabled();
    }
}
