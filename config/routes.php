<?php

/** @var \Mezzio\Application $app */

$app->get('/', App\Action\Index::class, 'index');
$app->post('/oauth', App\Action\OAuthToken::class, 'oauth.token');
$app->route('/oauth/authorize', App\Action\OAuthAuthorize::class, ['GET', 'POST'], 'oauth.authorize');
