<?php
namespace Magento\AcceptanceTestFramework\Generate;

/**
 * Create table with output for generated classes.
 */
class GenerateResult
{
    /**
     * Counter.
     *
     * @var int
     */
    protected static $cnt = 0;

    /**
     * Result output.
     *
     * @var array
     */
    protected static $results = [];

    /**
     * Add result.
     *
     * @param mixed $item
     * @param int $count
     * @param int $time
     * @return void
     */
    public static function addResult($item, $count, $time = 0)
    {
        self::$results[] = [
            'item' => $item,
            'count' => $count,
            'time' => $time
        ];
    }

    /**
     * Get generated results.
     *
     * @return array
     */
    public static function getResults()
    {
        return self::$results;
    }

    /**
     * Display results.
     *
     * @return void
     */
    public static function displayResults()
    {
        global $argv;

        $results = self::getResults();
        if (isset($argv)) {
            $lengths = [
                'item' => strlen('Item'),
                'count' => strlen('Count'),
                'time' => strlen('Time(s)')
            ];
            foreach ($results as $result) {
                $lengths['item'] = max($lengths['item'], strlen($result['item']));
                $lengths['count'] = max($lengths['count'], strlen($result['count']));
                $lengths['time'] = max($lengths['time'], strlen($result['time']));
            }
            echo "|| " . self::prepareForShell('Item', $lengths['item']);
            echo " || " . self::prepareForShell('Count', $lengths['count']);
            echo " || " . self::prepareForShell('Time(s)', $lengths['time']) . " ||\n";
            foreach ($results as $result) {
                echo "|| " . self::prepareForShell($result['item'], $lengths['item']);
                echo " || " . self::prepareForShell($result['count'], $lengths['count']);
                echo " || " . self::prepareForShell($result['time'], $lengths['time']) . " ||\n";
            }
            echo "\n";
        } else {
            echo '<table border="1" cellpadding="5"><thead><tr><th>Item</th><th>Count</th><th>Time</th></tr></thead><tbody>';
            foreach ($results as $result) {
                echo "<tr><td>{$result['item']}</td><td>{$result['count']}</td><td>{$result['time']}</td></tr>";
            }
            echo "</tbody></table>";
        }
    }

    /**
     * Prepare results for shell output.
     *
     * @param string $string
     * @param int $length
     * @return string
     */
    protected static function prepareForShell($string, $length)
    {
        $suffixLength = $length - strlen($string);
        $suffix = '';
        for ($i = 0; $suffixLength > $i; $i++) {
            $suffix .= ' ';
        }
        return $string . $suffix;
    }
}
