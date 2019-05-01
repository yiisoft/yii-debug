<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Debug\Panels;

use yii\base\Application;
use yii\helpers\Yii;
use yii\web\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays application configuration and environment.
 *
 * @property-read array $extensions Returns data about extensions.
 * @property-read array $phpInfo Returns the BODY contents of the phpinfo() output.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ConfigPanel extends Panel
{
    private $app;

    public function __construct(Application $app, View $view)
    {
        $this->app = $app;
        parent::__construct($view);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary(): string
    {
        return $this->render('panels/config/summary', ['panel' => $this]);
    }

    /**
     * {@inheritdoc}
     */
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
        $pinfo = ob_get_contents();
        ob_end_clean();
        $phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo);
        $phpinfo = str_replace('<table', '<div class="table-responsive"><table class="table table-condensed table-bordered table-striped table-hover config-php-info-table" ', $phpinfo);
        $phpinfo = str_replace('</table>', '</table></div>', $phpinfo);
        $phpinfo = str_replace('<div class="center">', '<div class="phpinfo">', $phpinfo);
        return $phpinfo;
    }

    /**
     * {@inheritdoc}
     */
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
