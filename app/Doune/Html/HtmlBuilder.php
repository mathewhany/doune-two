<?php

namespace Doune\Html;

class HtmlBuilder
{
    /**
     * Convert the given array to html attributes.
     *
     * @param array $attributes
     * @return string
     */
    public function getHtmlAttributes(array $attributes)
    {
        $htmlCode = '';

        foreach ($attributes as $name => $value) {
            $htmlCode .= $this->getHtmlAttribute($name, $value) . ' ';
        }

        return rtrim($htmlCode);
    }

    /**
     * Show the given name and value as html attribute, like this -> name="value"
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    public function getHtmlAttribute($name, $value)
    {
        if (is_bool($value)) {
            return $this->getHtmlAttribute($name, $value ? $name : null);
        }

        if ( ! is_null($value)) return $name . '="'. $value . '"';
    }
}