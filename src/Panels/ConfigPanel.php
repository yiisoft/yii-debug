<?php
namespace Yiisoft\Yii\Debug\Panels;

use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays application configuration and environment.
 */
class ConfigPanel extends Panel
{
    private $app;

    public function __construct(Application $app, View $view)
    {
        $this->app = $app;
        parent::__construct($view);
    }
    public function getName(): string
    {
        return 'Configuration';
    }
    public function getSummary(): string
    {
        return $this->render('panels/config/summary', ['panel' => $this]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/config/detail', ['panel' => $this]);
    }

    /**
     * Returns data about extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        $data = [];
        foreach ($this->data['extensions'] as $extension) {
            $data[$extension['name']] = $extension['version'];
        }
        ksort($data);

        return $data;
    }

    /**
     * Returns the BODY contents of the phpinfo() output
     *
     * @return array
     */
    public function getPhpInfo()
    {
        ob_start();
        phpinfo();
        $pinfo = ob_get_clean();
        $phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo);
        $phpinfo = str_replace(
            ['<table', '</table>', '<div class="center">'],
            [
                '<div class="table-responsive"><table class="table table-condensed table-bordered table-striped table-hover config-php-info-table" ',
                '</table></div>',
                '<div class="phpinfo">',
            ],
            $phpinfo
        );

        return $phpinfo;
    }
    public function save()
    {
        return [
            'phpVersion' => PHP_VERSION,
            'yiiVersion' => Yii::getVersion(),
            'application' => [
                'yii' => Yii::getVersion(),
                'name' => $this->app->name,
                'version' => $this->app->version,
                'locale' => (string)$this->app->locale,
                'language' => $this->app->language,
                'encoding' => $this->app->encoding,
                'env' => YII_ENV,
                'debug' => YII_DEBUG,
            ],
            'php' => [
                'version' => PHP_VERSION,
                'xdebug' => extension_loaded('xdebug'),
                'apc' => extension_loaded('apc'),
                'memcache' => extension_loaded('memcache'),
                'memcached' => extension_loaded('memcached'),
            ],
            'extensions' => [], // FIXME: $this->app->extensions
        ];
    }
}
