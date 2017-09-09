<?php

use Slim\Slim;

function app()
{
    return Slim::getInstance();
}

/**
 * Is it a valid article? If not, redirect back with input.
 *
 * @param string $title
 * @param string $content
 * @return void
 */
function validateArticle($title, $content)
{
    if (empty($title) || empty($content)) {
        redirect()->withError('Please enter all fields.')->withInput()->back();
    } elseif (mb_strlen($title, 'utf-8') > 255) {
        redirect()->withError('The title can\'t contain 256 characters or >.')->withInput()->back();
    }
}

/**
 * Validate the given setting.
 *
 * @param string $name
 * @param string $value
 * @return void
 */
function validateSetting($name, $value)
{
    if (in_array($name, ['title', 'keywords', 'description']) && empty($value)) {
        redirect()->withError('Please enter all fields')->withInput()->back();
    } else if ($name == 'default_theme' && !app()->theme_manager->hasTheme($value)) {
        redirect()->withError('Cannot find that theme!');
    }
}

function validateUser($email, $password)
{
    if (empty($email) || empty($password)) {
        redirect()->withError('Please enter all fields.')->withInput()->back();
    }
}

/**
 * @param string|null $url
 * @return \Doune\Helpers\Redirector
 */
function redirect($url = null)
{
    if (is_null($url)) return app()->redirector;

    return redirect()->to($url);
}