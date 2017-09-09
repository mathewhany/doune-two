<?php

$app->put('/articles/:id', [$app->auth, 'ownerMiddleware'], function ($id) use ($app) {

    $title = $app->request->put('title');
    $content = $app->request->put('content');
    $tags = $app->request->put('tags_list');

    validateArticle($title, $content);

    $article = Article::find($id);
    
    $article->update(compact('title', 'content'));

    $article->tags()->sync(Tag::process($tags));

    redirect()->withMessage('The article was edited successfully.')->route('articles.show', compact('id'));

})->name('articles.update');