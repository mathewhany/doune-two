<?php

namespace Doune\Auth;

class Auth
{
    public function ownerMiddleware(\Slim\Route $route)
    {
        $this->authMiddleware();

        if ($this->user()->id !== \Article::find($route->getParams()['id'])->user_id && ! $this->isAdmin()) {
            redirectToRouteWithError('home', 'You don\'t have permissions to do this action.');
        }
    }

    public function adminMiddleware()
    {
        if ( ! $this->isAdmin()) redirect()->route('home');
    }

    public function authMiddleware()
    {
        if ( ! $this->isLoggedIn()) redirect()->route('auth.login');
    }

    public function isLoggedIn()
    {
        return isset($_SESSION[$this->getName()]) && \User::find($_SESSION[$this->getName()]);
    }

    public function isAdmin()
    {
        return $this->isLoggedIn() && $this->user()->id == 1;
    }

    public function login($email, $password)
    {
        $user = \User::whereRaw('email = ? and password = ?', [$email, md5($password)]);

        if ($user->count() > 0) {
            return $_SESSION[$this->getName()] = $user->first()->id;
        }

        return false;
    }

    public function user()
    {
        if ( ! $this->isLoggedIn()) return;

        return \User::find($_SESSION[$this->getName()]);
    }

    public function logout()
    {
        session_destroy();
    }

    public function getName()
    {
        return 'login_' . md5(__CLASS__);
    }
}