<?php

$app->post('/register', function () use ($app) {

    $email = $app->request->post('email');
    $password = $app->request->post('password');
    
    validateUser($email, $password);

    User::create(['email' => $email, 'password' => md5($password)]);

    $app->auth->login($email, $password);
    
    redirect()->withMessage('Successfully!')->route('home');

})->name('auth.store');