<?php

namespace Doune\Helpers;

class Redirector
{
	protected $app;

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function withInput()
	{
		$this->flashInput();

		return $this;
	}

	public function withError($error)
	{
		return $this->withMessage($error, 'error');
	}

	public function withMessage($message, $type = 'success')
	{
		$this->app->flash($type, $message);

		return $this;
	}

	public function flashInput()
	{
		$this->app->flash('input', $this->app->request->params());
	}

	public function to($url)
	{
		$this->app->redirect($url);
	}

	public function back()
	{
		$this->to($this->app->request->getReferrer());
	}

	public function route($route, array $params = [])
	{
		$this->to($this->app->urlFor($route, $params));
	}
}