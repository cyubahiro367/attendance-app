<?php

use App\Models\Employee;

test("new employee can be created", function () {
    $data = [
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ];

    $response = $this->post('/employee', $data);

    $response->assertStatus(200);

    $this->assertDatabaseCount('Employee', 1);
    $this->assertDatabaseHas('Employee', [
        'names' =>  $data['names'],
        'email' => $data['email'],
        'employeeIdentifier' =>  $data['employeeIdentifier'],
        'phoneNumber' => $data['phoneNumber']
    ]);
});

test("Display all employees", function () {
    $employee = Employee::factory()->create([
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ]);

    $response = $this->get('/employee');

    $response->assertStatus(200);

    $response->assertExactJson(
        [
            [
                "id" => $employee->id,
                "names" => $employee->names,
                "email" => $employee->email,
                "employeeIdentifier" => $employee->employeeIdentifier,
                "phoneNumber" => $employee->phoneNumber
            ]
        ]
    );
});
