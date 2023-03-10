<?php
/*
 * This file is part of StringTemplate.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StringTemplate\Test;

use PHPUnit\Framework\TestCase;
use StringTemplate\Engine;

/**
 * Unit tests for class Engine
 */
class EngineTest extends TestCase
{
    public function testRenderWithScalarReplacement()
    {
        $engine = new Engine('<', '>');
        $this->assertEquals('my string and <your> string', $engine->render('my <> and <your> <>', 'string'));
    }

    public function testRender()
    {
        $engine = new Engine();
        $this->assertEquals(
            'Oh! The cat jumped onto the table',
            $engine->render(
                'Oh! {subj.det} {subj.np} {verb} onto {w.where.det} {w.where.np}',
                array(
                    'verb' => 'jumped',
                    'subj' => array('det' => 'The', 'np' => 'cat'),
                    'w' => array('where' => array('det' => 'the', 'np' => 'table'))
                )
            )
        );
    }

    public function testRenderWithCondition()
    {
        $engine = new Engine();
        $this->assertEquals(
            'Oh! Me',
            $engine->render(
                'Oh! {#name}Me{/name}',
                array(
                    'name' => true
                )
            )
        );
        $this->assertEquals(
            'Oh! Me',
            $engine->render(
                'Oh! {#name.value}Me{/name.value}',
                array(
                    'name' => ['value'=>true]
                )
            )
        );
        $this->assertEquals(
            'Oh! Me',
            $engine->render(
                'Oh! {#name}{test}{/name}',
                array(
                    'name' => function () {
                        return true;
                    },
                    'test' => 'Me'
                )
            )
        );

        $this->assertTrue(is_callable('strtoupper'));
        $this->assertEquals(
            'Oh! strtoupper',
            $engine->render(
                'Oh! {#name}{test}{#else}{else}{/name}',
                array(
                    'name' => false ,
                    'test' => 'Me',
                    'else' => 'strtoupper'
                )
            )
        );

        $this->assertEquals(
            'Oh! Him',
            $engine->render(
                'Oh! {#name.value}{test}{#else}{else.content}{/name.value}',
                array(
                    'name' => ['value' => false] ,
                    'test' => 'Me',
                    'else' => [ 'content' => 'Him']
                )
            )
        );

        $this->assertEquals(
            'Oh! Them',
            $engine->render(
                'Oh! {#name.value}{test}{#else}{#content}Them{/content}{/name.value}',
                array(
                    'name' => ['value' => false] ,
                    'test' => 'Me',
                    'content' => 'Him'
                )
            )
        );
    }

    public function testNoRightDelimiter()
    {
        $engine = new Engine(':', '');
        $this->assertEquals(
            'Oh! Me',
            $engine->render(
                'Oh! :name',
                array(
                    'name' => 'Me'
                )
            )
        );

        $this->assertEquals(
            'Oh! Him',
            $engine->render(
                'Oh! :#nameHim:/name',
                array(
                    'name' => true
                )
            )
        );

        $this->assertEquals(
            'Oh! Them',
            $engine->render(
                'Oh! :#name.value:test:#else:#contentThem:/content:/name.value',
                array(
                    'name' => ['value' => false] ,
                    'test' => 'Me',
                    'content' => 'Him'
                )
            )
        );
    }



    public function testSprintf()
    {
        $engine = new Engine();
        $this->assertEquals(
            sprintf('Oh! %s %s jumped onto %s (%e) table', 'The', 'cat', 1, 1),
            $engine->render(
                'Oh! {subj.det%s} {subj.np%s} {verb} onto {w.where.number%s} ({w.where.number%e}) {w.where.np}',
                array(
                    'verb' => 'jumped',
                    'subj' => array('det' => 'The', 'np' => 'cat'),
                    'w' => array('where' => array('number' => 1, 'np' => 'table'))
                )
            )
        );
    }

     public function testClosure()
     {
         $engine = new Engine();
         $this->assertEquals(
             'Oh John Doe',
             $engine->render(
                 'Oh {name}',
                 [
                     'first' => 'John',
                     'last' => 'Doe',
                     'name' => function ($values) {
                         return $values['first'].' '.$values['last'];
                     }
                 ]
             )
         );
     }


    public function testModifier()
    {
        $engine = new Engine(':', '');
        $this->assertEquals(
            'Oh! ME',
            $engine->render(
                'Oh! :name|upper',
                array(
                    'name' => 'Me'
                )
            )
        );

        try {
            $engine->render(
                'Oh! :name|strtoupper',
                array(
                    'name' => 'Me'
                )
            );
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), "Filter strtoupper not allowed");
        }
    }



    public function testCustomModifier()
    {
        $engine = new Engine(':', '', [
            'esc_html' => function ($string) {
                return htmlentities($string, ENT_NOQUOTES);
            }
        ]);
        $this->assertEquals(
            'Oh! &lt;script&gt;John&lt;/script&gt;',
            $engine->render(
                'Oh! :name|esc_html',
                array(
                    'name' =>  '<script>John</script>'
                )
            )
        );
    }

    public function testFixRegSprintfGlutony()
    {
        $engine = new Engine();
        $this->assertEquals(
            'These are bar and friend. Those are {goo.b} and {goo.v} %',
            $engine->render(
                'These are {foo} and {bar}. Those are {goo.b} and {goo.v} %',
                array(
                   'foo' => 'bar',
                   'bar' => 'friend',
                   'goo' => array('a' => 'b', 'c' => 'd')
                )
            )
        );
    }

    public function testRenderWithObjectValues()
    {
        $engine = new Engine();
        $this->assertEquals('foo', $engine->render('{value}', array('value' => new ObjectMock())));
    }
}

class ObjectMock
{
    public function __toString()
    {
        return 'foo';
    }
}
