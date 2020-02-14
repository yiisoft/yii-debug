<?php

namespace Yiisoft\Yii\Debug\Panels;

use Psr\Http\Message\RequestInterface;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays performance profiling info.
 */
class ProfilingPanel extends Panel
{
    /**
     * @var array current request profile timings
     */
    private $_models;
    private RequestInterface $request;

    public function __construct(RequestInterface $request, View $view)
    {
        $this->request = $request;
        parent::__construct($view);
    }
    public function getName(): string
    {
        return 'Profiling';
    }
    public function getSummary(): string
    {
        return $this->render('panels/profile/summary', [
            'memory' => sprintf('%.3f MB', $this->data['memory'] / 1048576),
            'time' => number_format($this->data['time'] * 1000) . ' ms',
            'panel' => $this
        ]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/profile/detail', [
            'panel' => $this,
            'memory' => sprintf('%.3f MB', $this->data['memory'] / 1048576),
            'time' => number_format($this->data['time'] * 1000) . ' ms',
        ]);
    }
    public function save()
    {
        $target = $this->module->profileTarget;
        $messages = $target->messages;
        return [
            'memory' => memory_get_peak_usage(),
            'time' => microtime(true) - YII_BEGIN_TIME,
            'messages' => $messages,
        ];
    }

    /**
     * Returns array of profiling models that can be used in a data provider.
     * @return array models
     */
    protected function getModels()
    {
        if ($this->_models === null) {
            $this->_models = [];

            if (isset($this->data['messages'])) {
                foreach ($this->data['messages'] as $seq => $message) {
                    $this->_models[] = [
                        'duration' => $message['endTime'] * 1000 - $message['beginTime'] * 1000, // in milliseconds
                        'category' => $message['category'],
                        'info' => $message['token'],
                        'level' => $message['nestedLevel'],
                        'timestamp' => $message['beginTime'] * 1000, //in milliseconds
                        'seq' => $seq,
                    ];
                }
            }
        }

        return $this->_models;
    }
}
