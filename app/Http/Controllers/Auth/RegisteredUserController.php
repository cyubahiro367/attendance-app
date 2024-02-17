<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use OpenApi\Attributes as OA;

class RegisteredUserController extends Controller
{
    #[OA\Post(
        tags: ['Auth'],
        path: '/register',
        description: 'Register new user',
        summary: 'Create new user in Attendance system',
    )]
    #[OA\RequestBody(
        description: 'Register',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'name',
                    format: 'string',
                    example: 'user'
                ),
                new OA\Property(
                    property: 'email',
                    format: 'string',
                    example: 'user@gmail.com'
                ),
                new OA\Property(
                    property: 'password',
                    format: 'string',
                    example: 'password'
                ),
                new OA\Property(
                    property: 'password_confirmation',
                    format: 'string',
                    example: 'password'
                ),
            ]
        ),
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully registered ',
        content: new OA\JsonContent(
            example: null
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Unprocessable entity',
        content: new OA\JsonContent(
            example: [
                'errors' => [
                    'Email has already been used',
                ],
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
        content: new OA\JsonContent(
            example: [
                'message' => 'Internal server error',
            ]
        )
    )]
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
