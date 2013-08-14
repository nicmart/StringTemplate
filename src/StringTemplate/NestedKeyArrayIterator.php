<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace StringTemplate;


use RecursiveArrayIterator;

/**
 * Class NestedKeyArrayIterator
 *
 * This iterator iterates recoursively through an array,
 * returning as the current key an imploded list of the stacked keys
 * separated by a given separator.
 *
 * Example:
 * <code>
 * $ary = [
 *      'foo' => 'bar',
 *      'baz' => ['foo' => ['uh' => 'ah'], 'oh' => 'eh']
 * ]
 * foreach (new NestedKeyArrayIterator($ary) as $key => $value) {
 *  echo "$key: value\n",
 * }
 * </code>
 * prints
 *  foo: bar
 *  baz.foo.uh: ah
 *  baz.oh: eh
 *
 * @package StringTemplate
 */
class NestedKeyArrayIterator extends \RecursiveIteratorIterator
{
    private $stack = array();
    private $keySeparator;

    /**
     * @param array $array      The array to iterate
     * @param string $separator The separator used in keys
     */
    public function __construct(array $array, $separator = '.')
    {
        $this->keySeparator = $separator;
        parent::__construct(new RecursiveArrayIterator($array));
    }

    /**
     * {@inheritdoc}
     */
    public function callGetChildren()
    {
        $this->stack[] = parent::key();
        return parent::callGetChildren();
    }

    /**
     * {@inheritdoc}
     */
    public function endChildren()
    {
        parent::endChildren();
        array_pop($this->stack);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $keys = $this->stack;
        $keys[] = parent::key();

        return implode($this->keySeparator, $keys);
    }
} 