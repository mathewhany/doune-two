<?php

$app->get('/login', function () use ($app) {
    
    if ($app->auth->isLoggedIn()) redirectToRoute('home');

    $title = $app->settings['title'] . ' » Login';

    $app->render('auth/login.html.twig', compact('title'));

})->name('auth.login');