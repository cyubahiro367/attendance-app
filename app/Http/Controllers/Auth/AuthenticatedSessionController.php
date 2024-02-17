<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */

    #[OA\Post(
        tags: ['Auth'],
        path: '/login',
        description: 'Log in user',
        summary: 'Log in user in system',
    )]
    #[OA\RequestBody(
        description: 'Login',
        content: new OA\JsonContent(
            properties: [
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
            ]
        ),
    )]
    #[OA\Response(
        response: 202,
        description: 'Already authenticated',
        content: new OA\JsonContent(
            example: [
                'message' => 'Authenticated.',
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully logged in',
        content: new OA\JsonContent(
            example: null
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Unprocessable entity',
        content: new OA\JsonContent(
            example: [
                'message' => 'These credentials do not match our records.',
                'errors' => [
                    'email' => [
                        'These credentials do not match our records.',
                    ],
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
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    #[OA\Post(
        tags: ['Auth'],
        path: '/logout',
        description: 'Log out user',
        summary: 'Log out currently logged in user',
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully logged out',
        content: new OA\JsonContent(
            example: null
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            example: [
                'message' => 'Unauthenticated.',
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
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
