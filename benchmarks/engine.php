<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

/**
 * This is a basic benchmark test for Engine...
 */
include '../vendor/autoload.php';

$engine = new \StringTemplate\Engine;
$replace = function ($template, $replacement) use ($engine)
{
    $engine->render($template, $replacement);
};
$template = "These are {foo} and {bar}. Those are {goo.b} and {goo.v}";
$vars = array(
    'foo' => 'bar',
    'baz' => 'friend',
    'goo' => array('a' => 'b', 'c' => 'd')
);


function benchmark($f, $template, $replacement, $title = '', $iterations = 100000)
{
    echo '<br><b>', $title, '</b><br>';
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++)
        $f($template, $replacement);
    $time = microtime(true) - $start;
    echo 'Time: ', $time, '<br>';
    echo 'Average: ', $time / $iterations, '<br>';
    echo 'MemoryPeak: ', memory_get_peak_usage();
}

benchmark($replace, $template, $vars, 'Engine benchmark');
