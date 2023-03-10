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
  * $engine->render('Oh! {#name}{test}{/name}', ['name' => true, 'test' => 'Me']);
 * //Prints "'Oh! Me'"
 * </code>
 */
class Engine
{
    protected $left;
    protected $right;
    protected $ifRegex;
    protected $varRegex;
    protected $defaultFilters = [
        'upper' => 'strtoupper',
        'lower' => 'strtolower',
        'esc_html' => 'htmlspecialchars'

    ];


    /**
     * @param string $left  The left delimiter
     * @param string $right The right delimiter
     * @param array $filters allowed filters
     */
    public function __construct($left = '{', $right = '}', $filters = [])
    {
        $this->left = preg_quote($left, '/');
        $this->right = preg_quote($right, '/');
        $this->ifRegex = $this->ifRegex();
        $this->varRegex = $this->varRegex();
        $this->allowedFilters = array_merge($this->defaultFilters, $filters);
    }

    /**
     * If regular expression
     * greps {#variablename}if true{/variablename}
     * and {#variablename}if true{#else) if false {/variablename}
     * @return     string  The regex
     */
    public function ifRegex()
    {
        return '/'.$this->left.'#([a-zA-Z0-9_.-]*)'.$this->right. // If {#variable}
            '(.+?)'. // Inside condition tag
            '('.$this->left.'#else'.$this->right.'(.+?))?'. // maybe {#else} tag and condition
            $this->left.'\/\1'.$this->right.'/'; // close if {/variable}
        return $reg;
    }

    /**
     * Variable Regex
     * greps {variablename}
     * or {variablename%format}
     * or {variablename|filter}
     * or {variablename%format|filter}
     * @return     string  The regex
     */
    public function varRegex()
    {
        return '/'.$this->left.
            '([a-zA-Z0-9_.-]*)'.  // The variable
            '(%[^' .($this->right ? $this->right : ' ') . ']+)?' . // maybe sprintf format
            '(\|([a-zA-Z0-9_]+))?'. // maybe simple filter
            $this->right.'/';
    }

    /**
     * @param string $template      The template string
     * @param string|array|NestedKeyArray $value   The value the template will be rendered with
     *
     * @return string The rendered template
     */
    public function render($template, $value)
    {
        $result = $template;

        if ($value instanceof NestedKeyArray) {
            $nestedKeyArray = $value;
        } else {
            if (!is_array($value)) {
                $value = array('' => $value);
            }
            $nestedKeyArray = new NestedKeyArray($value) ;
        }

        // handle if regex
        $result = preg_replace_callback($this->ifRegex, function ($matches) use ($nestedKeyArray) {
            $toEvaluate = $matches[1];
            $inside = $matches[2];

            if (isset($nestedKeyArray[$toEvaluate])) {
                if ($nestedKeyArray[$toEvaluate]) {
                    return $this->render($inside, $nestedKeyArray);
                }
            }

            $hasElse = isset($matches[3]) ? $matches[3] : false ;
            if ($hasElse && isset($matches[4])) {
                return $this->render($matches[4], $nestedKeyArray);
            }
        }, $result);

        // handle variables replacement
        $result = preg_replace_callback($this->varRegex, function ($matches) use ($nestedKeyArray) {
            $toEvaluate = $matches[1];

            if (isset($nestedKeyArray[$toEvaluate])) {
                $result = isset($matches[2]) && $matches[2] ? sprintf($matches[2], $nestedKeyArray[$toEvaluate]) : $nestedKeyArray[$toEvaluate] ;
                // Handle filters (modifiers) is
                if (isset($matches[3]) && isset($matches[4])) {
                    $filter = $matches[4];
                    if (isset($this->allowedFilters[$filter]) &&  is_callable($this->allowedFilters[$filter])) {
                        $result = $this->allowedFilters[$filter]($result);
                    } else {
                        throw new \Exception("Filter $filter not allowed");
                    }
                }
                return $result;
            }
            return $matches[0];
        }, $result);

        return $result;
    }
}
