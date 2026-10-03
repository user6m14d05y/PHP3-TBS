<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/cart', 'GET');
$user = \App\Models\User::find(1); // User 1 is Admin TBS
$request->setUserResolver(function () use ($user) { return $user; });
$response = $kernel->handle($request);
echo $response->getContent();
