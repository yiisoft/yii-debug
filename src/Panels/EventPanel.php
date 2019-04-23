<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Debug\Panels;

use Clue\GraphComposer\App;
use yii\base\Event;
use yii\helpers\Yii;
use yii\web\Application;
use Yiisoft\Debug\Panel;

/**
 * Debugger panel that collects and displays information about triggered events.
 *
 * > Note: this panel requires Yii framework version >= 2.0.14 to function and will not
 *   appear at lower version.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0.14
 */
class EventPanel extends Panel
{
    /**
     * @var array current request events
     */
    private $_events = [];


    /**
     * {@inheritdoc}
     */
    public function __construct(Application $application)
    {
        parent::__construct($application);
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Events';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return $this->app->view->render('panels/event/summary', [
            'panel' => $this,
            'eventCount' => count($this->data),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetail()
    {
        $searchModel = new \Yiisoft\Debug\Models\Search\Event();
        $dataProvider = $searchModel->search($this->app->request->get(), $this->data);

        return $this->app->view->render('panels/event/detail', [
            'panel' => $this,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        return $this->_events;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        $yiiVersion = Yii::getVersion();
        if (!version_compare($yiiVersion, '2.0.14', '>=') && strpos($yiiVersion, '-dev') === false) {
            return false;
        }

        return parent::isEnabled();
    }
}
