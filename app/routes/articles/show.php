<?php

$app->get('/articles/:id', function ($id) use ($app) {

    if (!$article = Article::find($id)) {
        $app->notFound();
    }
    
    $title = $article->title . ' | ' . $app->settings['title'];

    $tags = $article->tags->lists('name');

    $app->render(
        'articles/show.html.twig',
        compact('title', 'article', 'tags')
    );

})->name('articles.show');