<?php

$app->delete('/articles/:id', [$app->auth, 'ownerMiddleware'], function ($id) use ($app) {

    Article::destroy($id);

	redirect()->withMessage('The article was deleted successfully.')->route('home');

})->name('articles.destroy');