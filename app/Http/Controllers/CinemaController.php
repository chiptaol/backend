<?php

namespace App\Http\Controllers;

use App\Http\Requests\CinemaIndexRequest;
use App\Http\Resources\CinemaExtendedResource;
use App\Models\Cinema;
use App\Rules\CoordinateRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CinemaController extends Controller
{
    public function index()
    {
        $allCinemas = Cinema::query()
            ->orderBy('title')
            ->get();

        return CinemaExtendedResource::collection($allCinemas);
    }

    public function indexNearest(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'longitude' => [new CoordinateRule()]
//        ])
    }

}
