<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashbaord\Auth\SignInFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function signIn(SignInFormRequest $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $user = User::where('email', '=', $request->validated('email'))->first();
        if (!Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message'  => trans('Invalid password.')
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->noContent()->withCookie(accessTokenCookie($user->createToken($user->id)->plainTextToken));
    }

    public function signOut(): \Illuminate\Http\Response
    {
        auth()->user()->tokens()->delete();

        return response()
            ->noContent()
            ->withoutCookie(cookie('Authorization', path: '/api/dashboard', domain: config('app.url')));
    }

    public function me(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return auth()->user();
    }
}
