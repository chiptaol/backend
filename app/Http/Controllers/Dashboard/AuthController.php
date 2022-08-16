<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashbaord\Auth\SignInFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @Oa\Post (
     *     path="/api/dashboard/auth/sign-in",
     *     tags={"Dashboard Auth"},
     *     summary="Sign in to dashboard",
     *
     *     @OA\RequestBody (
     *          required=true,
     *          @OA\JsonContent (
     *              required={"email", "password"},
     *              @OA\Property (property="email", type="string", example="bakhadyrovfbb@gmail.com"),
     *              @OA\Property (property="password", type="string", example="123455678A")
     *          )
     *     ),
     *
     *     @OA\Response (
     *          response=204,
     *          description="Success (No content)",
     *     ),
     *
     *     @OA\Response (
     *          response=422,
     *          description="Email does not exist",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="Выбранное значение для email некорректно.")
     *          )
     *     ),
     *
     *     @OA\Response (
     *          response=401,
     *          description="Invalid password",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="Неверный пароль.")
     *          )
     *     )
     *)
     *
     * @param SignInFormRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function signIn(SignInFormRequest $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $user = User::where('email', '=', $request->validated('email'))->first();
        if (!Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message'  => trans('Invalid password provided.')
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->noContent()->withCookie(accessTokenCookie($user->createToken($user->id)->plainTextToken));
    }

    /**
     * @OA\Post (
     *     path="/api/dashboard/auth/sign-out",
     *     tags={"Dashboard Auth"},
     *     summary="Sign out from dashboard",
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (No content)"
     *     ),
     *
     *     @OA\Response (
     *          response=401,
     *          description="Unauthenticated (Unauthorized)",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", default="Не аутентифицирован.")
     *          )
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function signOut(): \Illuminate\Http\Response
    {
        auth()->user()->tokens()->delete();

        return response()
            ->noContent()
            ->withoutCookie(cookie('Authorization', path: '/api/dashboard', domain: config('app.url')));
    }

    /**
     * @OA\Get (
     *     path="/api/dashboard/me",
     *     tags={"Dashboard Auth"},
     *     summary="Get the current authenticated user",
     *
     *     @OA\Response (
     *          response=200,
     *          description="Success (OK)",
     *          @OA\JsonContent (
     *              @OA\Property (property="email", type="string", example="bakhadyrovfbb@gmail.com"),
     *              @OA\Property (property="created_at", type="string", example="2022-08-15T20:19:33.000000Z"),
     *              @OA\Property (property="updated_at", type="string", example="2022-08-15T20:20:33.000000Z")
     *          )
     *     ),
     *
     *     @OA\Response (
     *          response=401,
     *          description="Unauthenticated (Unauthorized)",
     *          @OA\JsonContent (
     *              @OA\Property (property="message", type="string", example="Не аутентифирован.")
     *          )
     *     )
     * )
     *
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function me(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return auth()->user();
    }
}
