<?php
namespace Yiisoft\Yii\Debug;

use yii\base\Application;
use yii\base\Component;
use yii\helpers\Url;
use yii\view\ViewContextInterface;
use yii\web\View;
use Yiisoft\Arrays\ArrayHelper;

/**
 * Panel is a base class for debugger panel classes. It defines how data should be collected,
 * what should be displayed at debug toolbar and on debugger details view.
 *
 * @property string $detail Content that is displayed in debugger detail view. This property is read-only.
 * @property string $name Name of the panel. This property is read-only.
 * @property string $summary Content that is displayed at debug toolbar. This property is read-only.
 * @property string $url URL pointing to panel detail view. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Panel extends Component implements ViewContextInterface
{
    /**
     * @var string panel unique identifier.
     * It is set automatically by the container module.
     */
    public $id;
    /**
     * @var string request data set identifier.
     */
    public $tag;
    /**
     * @var Module
     */
    public $module;
    /**
     * @var mixed data associated with panel
     */
    public $data;
    /**
     * @var array array of actions to add to the debug modules default controller.
     * This array will be merged with all other panels actions property.
     * See [[\yii\base\Controller::actions()]] for the format.
     */
    public $actions = [];

    /**
     * @var FlattenException|null Error while saving the panel
     * @since 2.0.10
     */
    protected $error;
    /** @var View */
    protected $view;


    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @return string name of the panel
     */
    public function getName(): string
    {
        return '';
    }

    /**
     * @return string content that is displayed at debug toolbar
     */
    public function getSummary(): string
    {
        return '';
    }

    /**
     * @return string content that is displayed in debugger detail view
     */
    public function getDetail(): string
    {
        return '';
    }

    /**
     * Saves data to be later used in debugger detail view.
     * This method is called on every page where debugger is enabled.
     *
     * @return mixed data to be saved
     */
    public function save()
    {
        return null;
    }

    /**
     * Loads data into the panel
     *
     * @param mixed $data
     */
    public function load($data)
    {
        $this->data = $data;
    }

    /**
     * @param null|array $additionalParams Optional additional parameters to add to the route
     * @return string URL pointing to panel detail view
     */
    public function getUrl($additionalParams = null): string
    {
        $route = [
            '/' . $this->module->id . '/default/view',
            'panel' => $this->id,
            'tag' => $this->tag,
        ];

        if (is_array($additionalParams)) {
            $route = ArrayHelper::merge($route, $additionalParams);
        }

        return Url::toRoute($route);
    }

    /**
     * Returns a trace line
     * @param array $options The array with trace
     * @return string the trace line
     * @since 2.0.7
     */
    public function getTraceLine(array $options): string
    {
        if (!isset($options['text'])) {
            $options['text'] = "{$options['file']}:{$options['line']}";
        }
        $traceLine = $this->module->traceLine;
        if ($traceLine === false) {
            return $options['text'];
        }

        $options['file'] = str_replace('\\', '/', $options['file']);
        $rawLink = $traceLine instanceof \Closure ? $traceLine($options, $this) : $traceLine;
        return strtr($rawLink, ['{file}' => $options['file'], '{line}' => $options['line'], '{text}' => $options['text']]);
    }

    /**
     * @param FlattenException $error
     * @since 2.0.10
     */
    public function setError(FlattenException $error)
    {
        $this->error = $error;
    }

    /**
     * @return FlattenException|null
     * @since 2.0.10
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     * @since 2.0.10
     */
    public function hasError()
    {
        return $this->error !== null;
    }

    /**
     * Checks whether this panel is enabled.
     * @return bool whether this panel is enabled.
     * @since 2.0.10
     */
    public function isEnabled(): bool
    {
        return true;
    }
    public function getViewPath()
    {
        return __DIR__ . '/views/default';
    }

    /**
     * Renders the view specified with optional parameters.
     * The view will be rendered using the [[view]] component.
     * @param string $view a view name or a path alias of the view file.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return string the rendering result.
     */
    public function render(string $view, array $params = []): string
    {
        return $this->view->render($view, $params, $this);
    }
}
