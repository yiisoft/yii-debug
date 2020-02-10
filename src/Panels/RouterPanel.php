<?php
namespace Yiisoft\Yii\Debug\Panels;

use Psr\Log\LogLevel;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * RouterPanel provides a panel which displays information about routing process.
 */
class RouterPanel extends Panel
{
    /**
     * @var array
     */
    private $_categories = [
        'yii\web\UrlManager::parseRequest',
        'yii\web\UrlRule::parseRequest',
        'yii\web\CompositeUrlRule::parseRequest',
        'Yiisoft\Yii\Rest\UrlRule::parseRequest'
    ];

    private $app;

    public function __construct(Application $app, View $view)
    {
        $this->app = $app;
        parent::__construct($view);
    }

    /**
     * @param string|array $values
     */
    public function setCategories($values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        $this->_categories = array_merge($this->_categories, $values);
    }

    /**
     * Listens categories of the messages.
     * @return array
     */
    public function getCategories()
    {
        return $this->_categories;
    }
    public function getName(): string
    {
        return 'Router';
    }
    public function getSummary(): string
    {
        return $this->render('panels/router/summary', ['panel' => $this]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/router/detail', ['model' => new Router($this->data)]);
    }
    public function save()
    {
        $target = $this->module->logTarget;
        if ($this->app->requestedAction) {
            if ($this->app->requestedAction instanceof InlineAction) {
                $action = get_class($this->app->requestedAction->controller) . '::' . $this->app->requestedAction->actionMethod . '()';
            } else {
                $action = get_class($this->app->requestedAction) . '::run()';
            }
        } else {
            $action = null;
        }
        return [
            'messages' => $target::filterMessages($target->getMessages(), [LogLevel::DEBUG], $this->_categories),
            'route' => $this->app->requestedAction ? $this->app->requestedAction->getUniqueId() : $this->app->requestedRoute,
            'action' => $action,
        ];
    }
}
