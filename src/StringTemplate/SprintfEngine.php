<?php
/*
 * This file is part of StringTemplate.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StringTemplate;

/**
 * Class Engine
 *
 * Replace placeholder in strings with nested (array) values, allowing an optional
 * sprintf-like parameter after the placeholder name
 *
 * Example:
 * <code>
 * $engine->render('This is {a} and these are {c.0%E} and {c.1}', ['a' => 'b', 'c' => [1, 'e']]);
 * //Prints "This is b and these are d and 1.000000E+0"
 * </code>
 */
class SprintfEngine extends AbstractEngine
{
    /**
     * {@inheritdoc}
     */
    public function render($template, $value)
    {
        $result = $template;
        if (!is_array($value))
            $value = array('' => $value);

        foreach (new NestedKeyIterator(new \RecursiveArrayIterator($value)) as $key => $value) {
            $pattern = "/" . $this->left . $key . "(%[^" . $this->right . "])?" . $this->right . "/";
            preg_match_all($pattern, $template, $matches);
            $substs = array_map(function ($match) use ($value) {
                return $match !== '' ? sprintf($match, $value) : $value;
            }, $matches[1]);
            $result = str_replace($matches[0], $substs, $result);
        }
        return $result;
    }
}