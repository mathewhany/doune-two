<?php

namespace Doune\Html;

use Doune\Session\SessionManagerInterface;
use Doune\Url\UrlGeneratorInterface;

class FormBuilder
{
    /**
     * The model. (optional)
     *
     * @var object
     */
    protected $model;

    /**
     * The HTML builder.
     *
     * @var HtmlBuilder
     */
    protected $html;

    /**
     * The url generator.
     *
     * @var UrlGeneratorInterface
     */
    protected $url;

    /**
     * The session manager.
     *
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @param HtmlBuilder $html
     * @param UrlGeneratorInterface $url
     * @param SessionManagerInterface $session
     */
    public function __construct(HtmlBuilder $html, UrlGeneratorInterface $url, SessionManagerInterface $session)
    {
        $this->html = $html;
        $this->url = $url;
        $this->session = $session;
    }

    /**
     * Open a form, and use the form model binding.
     *
     * @param object $model
     * @param string $method
     * @param array $options
     * @return string
     */
    public function model($model, $method, $options = [])
    {
        $this->model = $model;

        return $this->open($method, $options);
    }

    /**
     * Open a new form.
     *
     * @param string $method
     * @param array $options
     * @return string
     */
    public function open($method, array $options = [])
    {
        $method = strtoupper($method);

        $append = $this->getAppendage($method);

        if ($method !== 'GET') {
            $method = 'POST';
        }

        $action = $this->getAction($options);

        unset($options['route']);

        $attributes = $this->html->getHtmlAttributes(
            array_merge(compact('method', 'action'), $options)
        );

        return "<form {$attributes}>{$append}";
    }

    /**
     * Close the form.
     *
     * @return string
     */
    public function close()
    {
        $this->model = null;

        return '</form>';
    }

    /**
     * Add a new input with the given type, name, value and options (attributes)
     *
     * @param string $type
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function input($type, $name, $value = null, $options = [])
    {
        if ( ! in_array($type, ['password', 'file', 'checkbox', 'radio'])) {
            $value = $this->getInputValue($name, $value);
        }

        $options = array_merge(
            compact('type', 'name'),
            $options
        );

        if ( ! is_null($value)) {
            $options = array_merge(compact('value'), $options);
        }

        $attributes = $this->html->getHtmlAttributes($options);

        return "<input {$attributes} />";
    }

    /**
     * Create a new hidden input.
     *
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function hidden($name, $value = null, array $options = [])
    {
        return $this->input('hidden', $name, $value, $options);
    }

    /**
     * Create a new text input.
     *
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function text($name, $value = null, array $options = [])
    {
        return $this->input('text', $name, $value, $options);
    }

    /**
     * Create a new email input.
     *
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function email($name, $value = null, array $options = [])
    {
        return $this->input('email', $name, $value, $options);
    }

    /**
     * Create a new password input.
     *
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function password($name, $value = null, array $options = [])
    {
        return $this->input('password', $name, $value, $options);
    }

    /**
     * Create a new textarea.
     *
     * @param string $name
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function textarea($name, $value = null, $options = [])
    {
        $value = $this->getInputValue($name, $value);

        $attributes = $this->html->getHtmlAttributes(
            array_merge(compact('name'), $options)
        );

        return "<textarea {$attributes}>{$value}</textarea>";
    }

    /**
     * Create a new button.
     *
     * @param string $label
     * @param bool $submit
     * @param array $options
     * @return string
     */
    public function button($label, $submit = false, array $options = [])
    {
        if ($submit) {
            $options['type'] = 'submit';
        }

        $attributes = $this->html->getHtmlAttributes($options);

        return "<button {$attributes}>{$label}</button>";
    }

    /**
     * Create a form select box.
     *
     * @param string $name
     * @param array $items
     * @param array $selected
     * @param array $options
     * @return string
     */
    public function select($name, array $items, $selected = [], array $options = [])
    {
        $attributes = $this->html->getHtmlAttributes(
            array_merge(compact('name'), $options)
        );

        $htmlCode = "<select {$attributes}>";
        
        $selected = $this->getInputValue($name, $selected) ?: [];

        foreach ($items as $value => $label) {
            $htmlCode .= $this->option($value, $label, in_array($value, $selected));
        }

        return $htmlCode . '</select>';
    }

    /**
     * Create a from select box option.
     *
     * @param string $value
     * @param string $label
     * @param bool $selected
     * @return string
     */
    public function option($value, $label, $selected = false)
    {
        $attributes = $this->html->getHtmlAttributes(compact('value', 'selected'));

        return "<option {$attributes}>{$label}</option>";
    }

    /**
     * Get the value of the given input.
     *
     * @param string $name
     * @param string|null $default
     * @return mixed
     */
    public function getInputValue($name, $default = null)
    {
        if (is_null($name)) return $default;

        if ( ! empty($default)) return $default;

        return $this->session->getOldInput(
            $name,
            $this->getModelAttribute(rtrim($name, '[]'))
        );
    }

    /**
     * Get the value of the given attribute from the model.
     *
     * @param string $name
     * @return mixed
     */
    public function getModelAttribute($name)
    {
        $model = $this->model ? (object) $this->model : null;

        return $model ? $model->$name : null;
    }

    /**
     * Get the appendage if the method is PUT, PATCH or DELETE.
     *
     * @param string $method
     * @return string
     */
    public function getAppendage($method)
    {
        if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
            return $this->hidden('_METHOD', $method);
        }
    }

    /**
     * Get the value of the action attribute.
     *
     * @param array $options
     * @return mixed
     */
    public function getAction(array $options)
    {
        if (isset($options['action'])) {
            return $options['action'];
        } elseif (isset($options['route'])) {
            return $this->url->route($options['route'][0], @$options['route'][1] ?: []);
        }

        return $this->url->current();
    }

    public function radio($name, $value, array $options = [])
    {
        $options['checked'] = $value == $this->getInputValue($name);

        return $this->input('radio', $name, $value, $options);
    }
}