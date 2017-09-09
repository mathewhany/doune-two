<?php

$app->post('/login', function () use ($app) {

    $email = $app->request->post('email');
    $password = $app->request->post('password');
    
    validateUser($email, $password);

    if ( ! $app->auth->login($email, $password)) {
        redirect()->withError('Email or password or both of them are incorrect.')->withInput()->back();
    }
    
    redirect()->withMessage('Successfully!')->route('home');

})->name('auth.login.check');