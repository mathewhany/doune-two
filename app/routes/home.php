<?php

$app->get('/', function () use ($app) {

    $articles = Article::orderBy('created_at', 'desc')->get();
    
    $title = $app->settings['title'] . ' » Homepage';

    $app->render('home.html.twig', compact('title', 'articles'));
    
})->name('home');