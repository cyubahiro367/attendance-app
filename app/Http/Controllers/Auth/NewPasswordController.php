<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class NewPasswordController extends Controller
{
    #[OA\Post(
        tags: ['Auth'],
        path: '/reset-password',
        description: 'Reset password',
        summary: 'Reset user password'
    )]
    #[OA\RequestBody(
        description: 'Resetting password',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'token',
                    format: 'string',
                    example: 'HVc0QkfrcT6WX-dZtFTGO_SxABHuP__ZaeoqczqWhSA'
                ),
                new OA\Property(
                    property: 'email',
                    format: 'string',
                    example: 'user@gmail.com'
                ),
                new OA\Property(
                    property: 'password',
                    format: 'string',
                    example: 'password@123'
                ),
                new OA\Property(
                    property: 'password_confirmation',
                    format: 'string',
                    example: 'password@123'
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Reset',
        content: new OA\JsonContent(example: [
            'status' => 'Your password has been reset.',
        ])
    )]
    #[OA\Response(
        response: 422,
        description: 'Unprocessable entity',
        content: new OA\JsonContent(example: [
            'message' => 'The password field confirmation does not match.',
            'errors' => [
                'password' => [
                    'The password field confirmation does not match.',
                ],
            ],
        ])
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal Server Error',
        content: new OA\JsonContent(example: [
            'message' => 'Internal server error',
        ])
    )]
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}
