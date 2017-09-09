<?php

$app->post('/articles', [$app->auth, 'authMiddleware'], function () use ($app) {

    $title = $app->request->post('title');
    $content = $app->request->post('content');
 	$tags = $app->request->post('tags_list');

    validateArticle($title, $content);

    $article = $app->auth->user()->articles()->create(compact('title', 'content'));

    $article->tags()->sync(Tag::process($tags));

    $id = $article->id;

    redirect()->withMessage('The article was created successfully.')->route('articles.show', compact('id'));

})->name('articles.store');