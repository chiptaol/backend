<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info (
 *     version="3.0.0",
 *     title="Chiptaol API Documentation",
 *     description="Open API Documentation for https://chiptaol.uz",
 *     @OA\Contact(
 *          email="bakhadyrovf@gmail.com"
 *     )
 * )
 *
 * @OA\Server (
 *     url="https://chiptaol.uz"
 * )
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
