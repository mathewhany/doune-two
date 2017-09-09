<?php

$app->get('/articles/:id/edit', [$app->auth, 'ownerMiddleware'], function ($id) use ($app) {

    if (!$article = Article::find($id)) {
        $app->notFound();
    }

    $title = $app->settings['title'] . ' » Edit Article';

    $tags = Tag::lists('name', 'id');

    $app->render('articles/edit.html.twig', compact('title', 'article', 'tags'));
    
})->name('articles.edit');
