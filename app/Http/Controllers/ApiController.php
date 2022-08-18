<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __invoke(string $name)
    {
        if (file_exists(storage_path('responses/' . $name . '.json'))) {
            return response()->file(storage_path('responses/' . $name) . '.json');
        }

        return response()->json([
            'message' => 'Example-response with this file name not found.'
        ], 404);

    }
}
