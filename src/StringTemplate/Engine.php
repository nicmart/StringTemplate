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
 * Replace placeholder in strings with nested (array) values.
 *
 * Example:
 * <code>
 * $engine->render('This is {a} and these are {c.0} and {c.1}', ['a' => 'b', 'c' => ['d', 'e']]);
 * //Prints "This is b and these are d and e"
 * </code>
 */
class Engine
{
    protected $left;
    protected $right;

    /**
     * @param string $left  The left delimiter
     * @param string $right The right delimiter
     */
    public function __construct($left = '{', $right = '}')
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @param string $template      The template string
     * @param string|array $value   The value the template will be rendered with
     *
     * @return string The rendered template
     */
    public function render($template, $value)
    {
        $result = $template;
        if (!is_array($value))
            $value = array('' => $value);

        foreach (new NestedKeyIterator(new \RecursiveArrayIterator($value)) as $key => $value) {
            $result = str_replace($this->left . $key . $this->right, $value, $result);
        }

        return $result;
    }
}