<?php

$app->get('/articles/create', [$app->auth, 'authMiddleware'], function () use ($app) {

    $title = $app->settings['title'] . ' » Create Article';

    $tags = Tag::lists('name', 'id');

    $app->render('articles/create.html.twig', compact('title', 'tags'));

})->name('articles.create');