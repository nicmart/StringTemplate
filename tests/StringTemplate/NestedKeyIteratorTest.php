<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace StringTemplate\Test;


use StringTemplate\NestedKeyIterator;

class NestedKeyIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteration()
    {
        $ary = array(
             1 => 'a',
            '2' => 'b',
            'third' => array(
                'a',
                'b',
                'c' => array(
                    1,
                    'miao' => 'bau'
                )
            )
        );

        $iterator = new NestedKeyIterator(new \RecursiveArrayIterator($ary));

        $iterator->next();
        $this->assertSame('1', $iterator->key());
        $this->assertSame('a', $iterator->current());

        $iterator->next();
        $this->assertSame('2', $iterator->key());
        $this->assertSame('b', $iterator->current());

        $iterator->next();
        $this->assertSame('third.0', $iterator->key());
        $this->assertSame('a', $iterator->current());

        $iterator->next();
        $this->assertSame('third.1', $iterator->key());
        $this->assertSame('b', $iterator->current());

        $iterator->next();
        $this->assertSame('third.c.0', $iterator->key());
        $this->assertSame(1, $iterator->current());

        $iterator->next();
        $this->assertSame('third.c.miao', $iterator->key());
        $this->assertSame('bau', $iterator->current());
    }
}
 