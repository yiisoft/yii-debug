<?php

namespace Yiisoft\Yii\Debug\Panels;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;
use Yiisoft\Yii\Web\Session\SessionInterface;

/**
 * Debugger panel that collects and displays request data.
 */
class RequestPanel extends Panel
{
    /**
     * @var array list of the PHP predefined variables that are allowed to be displayed in the request panel.
     * Note that a variable must be accessible via `$GLOBALS`. Otherwise it won't be displayed.
     */
    public $displayVars = ['_SERVER', '_GET', '_POST', '_COOKIE', '_FILES', '_SESSION'];

    private RequestInterface $request;
    private ResponseInterface $response;
    private SessionInterface $session;
    private $app;

    public function __construct(SessionInterface $session, ResponseInterface $response, RequestInterface $request, Application $app, View $view)
    {
        $this->session = $session;
        $this->request = $request;
        $this->response = $response;
        $this->app = $app;
        parent::__construct($view);
    }
    public function getName(): string
    {
        return 'Request';
    }
    public function getSummary(): string
    {
        return $this->render('panels/request/summary', ['panel' => $this]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/request/detail', ['panel' => $this]);
    }
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
