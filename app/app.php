<?php

use Illuminate\Support\Facades\Hash;

require_once __DIR__.'/helper.php';

function apiAuthToken() {
    $token = config('app.url') . config('app.key') . config('app.parent_url');
    $hashedToken = Hash::make($token);
    return 'Bearer '.$hashedToken;
}