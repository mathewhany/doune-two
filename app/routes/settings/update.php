<?php

$app->put('/settings/update', function () use ($app) {

    foreach ($app->request->put() as $name => $value) {

        if ($name == '_METHOD') continue;

        validateSetting($name, $value);

        Setting::whereName($name)->first()->update(compact('name', 'value'));
    }

    redirect()->withMessage('Saved ..!')->back();

})->name('settings.update');