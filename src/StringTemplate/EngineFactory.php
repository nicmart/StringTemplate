<?php

namespace StringTemplate;

/**
 * EngineFactory
 */
abstract class EngineFactory
{
    /**
     * @param string $name
     *
     * @return EngineInterface
     *
     * @throws \InvalidArgumentException
     */
    public static function create($name = 'regular')
    {
        $name = (string) $name;
        $mapping = static::getMapping();

        if (!isset($mapping[$name])) {
            throw new \InvalidArgumentException(sprintf('Unrecognized "%s" engine', $name));
        }

        $className = $mapping[$name];
        return new $className();
    }

    /**
     * @return array
     */
    protected static function getMapping()
    {
        return array(
            'regular' => 'StringTemplate\Engine',
            'sprintf' => 'StringTemplate\SprintfEngine'
        );
    }
}
