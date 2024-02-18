<?php

use App\Models\Employee;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

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

test("Update employee data", function () {
    $employee = Employee::factory()->create([
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ]);

    $data = [
        'names' => 'Test User 2',
        'email' => 'test2@example.com',
        'employeeIdentifier' => '123452',
        'phoneNumber' => '098767872',
    ];

    $response = $this->put("/employee/{$employee->id}", $data);

    $response->assertStatus(200);

    $this->assertDatabaseCount('Employee', 1);
    $this->assertDatabaseHas('Employee', [
        "id" => $employee->id,
        'names' =>  $data['names'],
        'email' => $data['email'],
        'employeeIdentifier' =>  $data['employeeIdentifier'],
        'phoneNumber' => $data['phoneNumber']
    ]);
});

test("Delete employee", function () {
    $employee = Employee::factory()->create([
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ]);

    $response = $this->delete("/employee/{$employee->id}");

    $response->assertStatus(200);

    $this->assertDatabaseCount('Employee', 0);
    $this->assertDatabaseMissing('Employee', [
        "id" => $employee->id,
        'names' =>  $employee->names,
        'email' => $employee->email,
        'employeeIdentifier' =>  $employee->employeeIdentifier,
        'phoneNumber' => $employee->phoneNumber
    ]);
});
