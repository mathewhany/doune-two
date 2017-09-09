<?php

namespace Doune\Helpers;

use Illuminate\Support\Str;

class TemplateHelper extends \Twig_Extension
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
	{
		return 'doune';
	}

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
	{
		return [
            new \Twig_SimpleFunction('asset', [$this->container->theme_manager, 'asset']),
            new \Twig_SimpleFunction('form_*', function ($method) {
                return call_user_func_array(
                    [$this->container->form, Str::camel($method)],
                    array_slice(func_get_args(), 1));
            }, ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('logged_in', [$this->container->auth, 'isLoggedIn']),
            new \Twig_SimpleFunction('admin', [$this->container->auth, 'isAdmin']),
            new \Twig_SimpleFunction('user', [$this->container->auth, 'user']),
		];
	}
}