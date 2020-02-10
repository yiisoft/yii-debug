<?php
namespace Yiisoft\Yii\Debug\Panels;

use Psr\Http\Message\RequestInterface;
use Psr\Log\LogLevel;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays database queries performed.
 */
class DbPanel extends Panel
{
    /**
     * @var int the threshold for determining whether the request has involved
     * critical number of DB queries. If the number of queries exceeds this number,
     * the execution is considered taking critical number of DB queries.
     */
    public $criticalQueryThreshold;
    /**
     * @var string the name of the database component to use for executing (explain) queries
     */
    public $db = 'db';
    /**
     * @var array the default ordering of the database queries. In the format of
     * [ property => sort direction ], for example: [ 'duration' => SORT_DESC ]
     */
    public $defaultOrder = [
        'seq' => SORT_ASC
    ];
    /**
     * @var array the default filter to apply to the database queries. In the format
     * of [ property => value ], for example: [ 'type' => 'SELECT' ]
     */
    public $defaultFilter = [];

    /**
     * @var array db queries info extracted to array as models, to use with data provider.
     */
    private $_models;
    /**
     * @var array current database request timings
     */
    private $_timings;

    private $request;
    public function __construct(ConnectionInterface $db, RequestInterface $request, View $view)
    {
        $this->db = $db;
        $this->request = $request;
        parent::__construct($view);
        $this->actions['db-explain'] = [
            '__class' => \Yiisoft\Yii\Debug\Actions\DB\ExplainAction::class,
            'panel' => $this,
        ];
    }
    public function getName(): string
    {
        return 'Database';
    }

    /**
     * @return string short name of the panel, which will be use in summary.
     */
    public function getSummaryName()
    {
        return 'DB';
    }
    public function getSummary(): string
    {
        $timings = $this->calculateTimings();
        $queryCount = count($timings);
        $queryTime = number_format($this->getTotalQueryTime($timings) * 1000) . ' ms';

        return $this->render('panels/db/summary', [
            'timings' => $this->calculateTimings(),
            'panel' => $this,
            'queryCount' => $queryCount,
            'queryTime' => $queryTime,
        ]);
    }
    public function getDetail(): string
    {
        $models = $this->getModels();
        $sumDuplicates = $this->sumDuplicateQueries($models);

        return $this->render('panels/db/detail', [
            'panel' => $this,
            'hasExplain' => $this->hasExplain(),
            'sumDuplicates' => $sumDuplicates,
        ]);
    }

    /**
     * Calculates given request profile timings.
     *
     * @return array timings [token, category, timestamp, traces, nesting level, elapsed time]
     */
    public function calculateTimings()
    {
        if ($this->_timings === null) {
            $this->_timings = [];
            if (isset($this->data['messages'])) {
                foreach ($this->data['messages'] as $seq => $message) {
                    $this->_timings[$seq] = [
                        'info' => $message['token'],
                        'category' => $message['category'],
                        'timestamp' => $message['beginTime'],
                        'trace' => $message['trace'],
                        'level' => $message['nestedLevel'],
                        'duration' => $message['endTime'] - $message['beginTime'],
                        'memory' => $message['endMemory'],
                        'memoryDiff' => $message['endMemory'] - $message['beginMemory'],
                    ];
                }
            }
        }

        return $this->_timings;
    }
    public function save()
    {
        return ['messages' => $this->getProfileLogs()];
    }

    /**
     * Returns all profile logs of the current request for this panel. It includes categories such as:
     * 'Yiisoft\Db\Command::query', 'Yiisoft\Db\Command::execute'.
     * @return array[]
     */
    public function getProfileLogs()
    {
        $categories = ['Yiisoft\Db\Command::query', 'Yiisoft\Db\Command::execute'];
        $profileTarget = $this->module->profileTarget;

        $logTarget = $this->module->logTarget;
        $logMessages = $logTarget === null
            ? []
            : $logTarget::filterMessages(
                $logTarget->getMessages(),
                [LogLevel::INFO, LogLevel::DEBUG],
                $categories
            );

        $messages = [];
        foreach ($profileTarget->messages as $message) {
            if (in_array($message['category'], $categories, true)) {
                $message['trace'] = [];
                foreach ($logMessages as $key => $logMessage) {
                    if ($logMessage[2]['category'] === $message['category'] && $logMessage[1] === $message['token']) {
                        $message['trace'] = $logMessage[2]['trace'];
                        unset($logMessages[$key]);
                        break;
                    }
                }

                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Returns total query time.
     *
     * @param array $timings
     * @return int total time
     */
    protected function getTotalQueryTime($timings)
    {
        $queryTime = 0;

        foreach ($timings as $timing) {
            $queryTime += $timing['duration'];
        }

        return $queryTime;
    }

    /**
     * Returns an  array of models that represents logs of the current request.
     * Can be used with data providers such as \yii\data\ArrayDataProvider.
     * @return array models
     */
    protected function getModels()
    {
        if ($this->_models === null) {
            $this->_models = [];
            $timings = $this->calculateTimings();
            $duplicates = $this->countDuplicateQuery($timings);

            foreach ($timings as $seq => $dbTiming) {
                $this->_models[] = [
                    'type' => $this->getQueryType($dbTiming['info']),
                    'query' => $dbTiming['info'],
                    'duration' => ($dbTiming['duration'] * 1000), // in milliseconds
                    'trace' => $dbTiming['trace'],
                    'timestamp' => ($dbTiming['timestamp'] * 1000), // in milliseconds
                    'seq' => $seq,
                    'duplicate' => $duplicates[$dbTiming['info']],
                ];
            }
        }

        return $this->_models;
    }

    /**
     * Return associative array, where key is query string
     * and value is number of occurrences the same query in array.
     *
     * @param $timings
     * @return array
     */
    public function countDuplicateQuery($timings)
    {
        $query = ArrayHelper::getColumn($timings, 'info');

        return array_count_values($query);
    }

    /**
     * Returns sum of all duplicated queries
     *
     * @param $modelData
     * @return int
     */
    public function sumDuplicateQueries($modelData)
    {
        $numDuplicates = 0;
        $duplicates = ArrayHelper::getColumn($modelData, 'duplicate');
        foreach ($duplicates as $duplicate) {
            if ($duplicate > 1) {
                $numDuplicates++;
            }
        }

        return $numDuplicates;
    }

    /**
     * Returns database query type.
     *
     * @param string $timing timing procedure string
     * @return string query type such as select, insert, delete, etc.
     */
    protected function getQueryType($timing)
    {
        $timing = ltrim($timing);
        preg_match('/^([a-zA-z]*)/', $timing, $matches);

        return count($matches) ? mb_strtoupper($matches[0], 'utf8') : '';
    }

    /**
     * Check if given queries count is critical according settings.
     *
     * @param int $count queries count
     * @return bool
     */
    public function isQueryCountCritical($count)
    {
        return (($this->criticalQueryThreshold !== null) && ($count > $this->criticalQueryThreshold));
    }

    /**
     * Returns array query types
     *
     * @return array
     */
    public function getTypes()
    {
        return array_reduce(
            $this->_models,
            function ($result, $item) {
                $result[$item['type']] = $item['type'];
                return $result;
            },
            []
        );
    }
    public function isEnabled(): bool
    {
        try {
            $this->getDb();
        } catch (\Throwable $exception) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * @return bool Whether the DB component has support for EXPLAIN queries
     */
    protected function hasExplain()
    {
        $db = $this->getDb();
        if (!($db instanceof \Yiisoft\Db\Connection)) {
            return false;
        }
        switch ($db->getDriverName()) {
            case 'mysql':
            case 'sqlite':
            case 'pgsql':
            case 'cubrid':
                return true;
            default:
                return false;
        }
    }

    /**
     * Check if given query type can be explained.
     *
     * @param string $type query type
     * @return bool
     */
    public static function canBeExplained($type)
    {
        return $type !== 'SHOW';
    }

    /**
     * Returns a reference to the DB component associated with the panel
     *
     * @return \Yiisoft\Db\Connection
     */
    public function getDb()
    {
        return $this->db;
    }
}
