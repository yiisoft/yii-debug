<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Debug\Panels;

use yii\base\Application;
use yii\base\InlineAction;
use yii\base\Request;
use yii\base\Response;
use yii\web\Session;
use yii\web\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays request data.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RequestPanel extends Panel
{
    /**
     * @var array list of the PHP predefined variables that are allowed to be displayed in the request panel.
     * Note that a variable must be accessible via `$GLOBALS`. Otherwise it won't be displayed.
     * @since 2.0.10
     */
    public $displayVars = ['_SERVER', '_GET', '_POST', '_COOKIE', '_FILES', '_SESSION'];

    private $request;
    private $response;
    private $session;
    private $app;

    public function __construct(Session $session, Response $response, Request $request, Application $app, View $view)
    {
        $this->session = $session;
        $this->request = $request;
        $this->response = $response;
        $this->app = $app;
        parent::__construct($view);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Request';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary(): string
    {
        return $this->render('panels/request/summary', ['panel' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetail(): string
    {
        return $this->render('panels/request/detail', ['panel' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $headers = $this->request->getHeaders();
        $requestHeaders = [];
        foreach ($headers as $name => $value) {
            if (is_array($value) && count($value) == 1) {
                $requestHeaders[$name] = current($value);
            } else {
                $requestHeaders[$name] = $value;
            }
        }

        $responseHeaders = [];
        foreach (headers_list() as $header) {
            if (($pos = strpos($header, ':')) !== false) {
                $name = substr($header, 0, $pos);
                $value = trim(substr($header, $pos + 1));
                if (isset($responseHeaders[$name])) {
                    if (!is_array($responseHeaders[$name])) {
                        $responseHeaders[$name] = [$responseHeaders[$name], $value];
                    } else {
                        $responseHeaders[$name][] = $value;
                    }
                } else {
                    $responseHeaders[$name] = $value;
                }
            } else {
                $responseHeaders[] = $header;
            }
        }

        if ($this->app->requestedAction) {
            if ($this->app->requestedAction instanceof InlineAction) {
                $action = get_class($this->app->requestedAction->controller) . '::' . $this->app->requestedAction->actionMethod . '()';
            } else {
                $action = get_class($this->app->requestedAction) . '::run()';
            }
        } else {
            $action = null;
        }

        $data = [
            'flashes' => $this->getFlashes(),
            'statusCode' => $this->response->getStatusCode(),
            'requestHeaders' => $requestHeaders,
            'responseHeaders' => $responseHeaders,
            'route' => $this->app->requestedAction ? $this->app->requestedAction->getUniqueId() : $this->app->requestedRoute,
            'action' => $action,
            'actionParams' => $this->app->requestedParams,
            'general' => [
                'method' => $this->request->getMethod(),
                'isAjax' => $this->request->getIsAjax(),
                'isFlash' => $this->request->getIsFlash(),
                'isSecureConnection' => $this->request->getIsSecureConnection(),
            ],
            'requestBody' => $this->request->getRawBody() == '' ? [] : [
                'Content Type' => $this->request->getContentType(),
                'Raw' => $this->request->getRawBody(),
                'Decoded to Params' => $this->request->getBodyParams(),
            ],
        ];

        foreach ($this->displayVars as $name) {
            $data[trim($name, '_')] = empty($GLOBALS[$name]) ? [] : $GLOBALS[$name];
        }

        return $data;
    }

    /**
     * Getting flash messages without deleting them or touching deletion counters
     *
     * @return array flash messages (key => message).
     */
    protected function getFlashes()
    {
        if ($this->session === null || !$this->session->getIsActive()) {
            return [];
        }

        $counters = $this->session->get($this->session->flashParam, []);
        $flashes = [];
        foreach (array_keys($counters) as $key) {
            if (array_key_exists($key, $_SESSION)) {
                $flashes[$key] = $_SESSION[$key];
            }
        }
        return $flashes;
    }
}
