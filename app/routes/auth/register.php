<?php

$app->get('/register', function () use ($app) {

    $title = $app->settings['title'] . '» Register';

    $app->render('auth/register.html.twig', compact($title));

})->name('auth.register');