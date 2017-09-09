<?php

$app->get('/logout', function () use ($app) {

    if ( ! $app->auth->isLoggedIn()) redirectToRoute('home');

    $app->auth->logout();

    redirect()->withMessage('You\'ve logged out successfully!')->route('home');

})->name('auth.logout');