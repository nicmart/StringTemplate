<?php

namespace StringTemplate;

/**
 * EngineInterface
 */
interface EngineInterface
{
    /**
     * @param string $template      The template string
     * @param string|array $value   The value the template will be rendered with
     *
     * @return string The rendered template
     */
    public function render($template, $value);
}
