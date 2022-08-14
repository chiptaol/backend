<?php

function accessTokenCookie(string $token)
{
    if (app()->environment('local')) {
        return cookie('Authorization','Bearer ' . $token, 60 * 24 * 90, '/api/dashboard');
    }

    return cookie('Authorization', 'Bearer ' . $token, 60 * 24 * 90, '/api/dashboard', config('app.url'), true, true ,false, 'none');
}
