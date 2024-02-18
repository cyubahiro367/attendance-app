<?php

use App\Models\User;
use Dotenv\Exception\ValidationException;

test('new users can register', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post('/register', $data);

    $response->assertStatus(204);
    $this->assertAuthenticated();
    $response->assertNoContent();

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('users', [
        'name' =>  $data['name'],
        'email' => $data['email']
    ]);
});

test("Through errors if there are errors while creating new users", function (array $data) {
    $response = $this->post('/register', $data);
    $response->assertStatus(302);
})->with([
    // email taken
    [
        function () {
            User::factory()->create([
                'email' => 'test@example.com',
            ]);

            return [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ];
        }
    ],

    // The password field confirmation does not match.
    [
        function () {
            return [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'passwords',
            ];
        }
    ],

    // name field required
    [
        function () {
            return [
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ];
        }
    ],

    // email field required
    [
        function () {
            return [
                'name' => 'Test User',
                'password' => 'password',
                'password_confirmation' => 'password',
            ];
        }
    ],

    // password filed required
    [
        function () {
            return [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password_confirmation' => 'password',
            ];
        }
    ],

    // password_confirmation field required 
    [
        function () {
            User::factory()->create([
                'email' => 'test@example.com',
            ]);

            return [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
            ];
        }
    ],

    // The password field must be a string.
    [
        function () {
            return [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 1234578,
                'password_confirmation' => 1234578,
            ];
        }
    ],

    // The password field must be at least 8 characters.
    [
        function () {
            return [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => "12345",
                'password_confirmation' => "12345",
            ];
        }
    ]
]);
