# Collectors

Read more about collectors in the [Collector](../collector.md) section.

## Summary collector

Summary collector is a collector that provides additional "summary" payload.
The summary payload is used to reduce time to read usual payload and summarise some metrics to get better UX.

Summary collector is usual collector with the additional method `getSummary()`.
Take a look at the [`\Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface`](./src/Collector/SummaryCollectorInterface.php):

```php
namespace Yiisoft\Yii\Debug\Collector;

/**
 * Summary data collector responsibility is to collect summary data for a collector.
 * Summary is used to display a list of previous requests and select one to display full info.
 * Its data set is specific to the list and is reduced compared to full data collected
 * in {@see CollectorInterface}.
 */
interface SummaryCollectorInterface extends CollectorInterface
{
    /**
     * @return array Summary payload. Keys may cross with any other summary collectors.
     */
    public function getSummary(): array;
}
```

We suggest you to give short names to your summary payload to be able to read the keys and decide to use them or not.

```php
    // with getCollected you can inspect all collected payload
    public function getCollected(): array
    {
        return $this->requests;
    }

    // getSummary gives you short description of the collected data just to decide inspect it deeper or not
    public function getSummary(): array
    {
        return [
            'web' => [
                'totalRequests' => count($this->requests),
            ],
        ];
    }
```
