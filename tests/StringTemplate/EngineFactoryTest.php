<?php

namespace StringTemplate\Test;

use StringTemplate\EngineFactory;

/**
 * Unit tests for the factory
 */
class EngineFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnrecognizedEngine()
    {
        EngineFactory::create('test');
    }

    public function testDefault()
    {
        $this->assertInstanceOf('StringTemplate\Engine', EngineFactory::create());
    }

    public function testRegular()
    {
        $this->assertInstanceOf('StringTemplate\Engine', EngineFactory::create('regular'));
    }

    public function testSprintf()
    {
        $this->assertInstanceOf('StringTemplate\SprintfEngine', EngineFactory::create('sprintf'));
    }
}
