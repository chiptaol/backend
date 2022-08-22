<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\FormatResource;
use App\Models\Format;
use Illuminate\Http\Request;

class FormatController extends Controller
{
    /**
     *
     * @OA\Get (
     *     path="/api/dashboard/formats",
     *     summary="Get a list of formats",
     *     tags={"Dashboard Halls"},
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK) [Response](https://api.chiptaol.uz/api/example-responses/dashboard-formats)"
     *     )
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return FormatResource::collection(Format::all());
    }
}
