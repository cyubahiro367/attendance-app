<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class PasswordResetLinkController extends Controller
{
    #[OA\Post(
        tags: ['Auth'],
        path: '/forgot-password',
        description: 'Send a reset password link to user',
        summary: "Send rest password link to user's email"
    )]
    #[OA\RequestBody(
        description: "User's email",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'email',
                    format: 'string',
                    example: 'user@gmail.com'
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(example: [
            'status' => 'We have emailed your password reset link.',
        ])
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
        response: 422,
        description: 'Unprocessable entity',
        content: new OA\JsonContent(example: [
            'message' => "We can't find a user with that email address.",
            'errors' => [
                'email' => ["We can't find a user with that email address."],
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
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}
