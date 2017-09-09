<?php

$app->get('/settings/edit', function () use ($app) {

    $title = $app->settings['title'] . ' » Settings';

    $themes = $app->theme_manager->automaticDetect();

    $app->render('settings/edit.html.twig', compact('title', 'themes'));

})->name('settings.edit');