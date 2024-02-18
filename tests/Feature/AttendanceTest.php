<?php

use App\Enum\AttendanceType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

test("new Attendance can be created", function () {
    $employee = Employee::factory()->create([
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ]);

    $data = [
        "employeeID" => $employee->id,
        "type" => AttendanceType::ARRIVE->value,
        "date" => "2023-03-02",
        "time" => "06:12"
    ];

    $response = $this->post('/attendance', $data);

    $response->assertStatus(200);

    $this->assertDatabaseCount('Attendance', 1);
    $this->assertDatabaseHas('Attendance', [
        'employeeID' =>  $data['employeeID'],
        'type' => $data['type'],
        'date' => Carbon::parse($data['date'])->setHour(0)->setMinute(0)->getTimestamp(),
        'time' =>  $data['time']
    ]);
});

test("Display all Attendances", function () {
    $employee = Employee::factory()->create([
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ]);

    $data = [
        "employeeID" => $employee->id,
        "type" => AttendanceType::ARRIVE->value,
        "date" => "2023-03-02",
        "time" => "06:12"
    ];

    $this->post('/attendance', $data);

    $response = $this->get('/attendance/2023-03-01/2024-03-01');

    $response->assertStatus(200);

    $response->assertJson(
        [
            [
                "names" => $employee->names,
                "user" => $this->user->name,
                "date" => $data['date'],
                "time" => $data['time']
            ]
        ]
    );
});

test("Update Attendance data", function () {
    $employee = Employee::factory()->create();

    $attendance = Attendance::factory()->create();

    $data = [
        "employeeID" => $employee->id,
        "type" => AttendanceType::ARRIVE->value,
        "date" => "2023-03-19",
        "time" => "06:12"
    ];

    $response = $this->put("/attendance/{$attendance->id}", $data);

    $response->assertStatus(200);

    $this->assertDatabaseCount('Attendance', 1);
    $this->assertDatabaseHas('Attendance', [
        'employeeID' =>  $data['employeeID'],
        'type' => $data['type'],
        'date' => Carbon::parse($data['date'])->setHour(0)->setMinute(0)->getTimestamp(),
        'time' =>  $data['time']
    ]);
});

test("Delete Attendance", function () {
    Employee::factory()->create();

    $attendance = Attendance::factory()->create();

    $response = $this->delete("/attendance/{$attendance->id}");

    $response->assertStatus(200);

    $this->assertDatabaseCount('Attendance', 0);
    $this->assertDatabaseMissing('Attendance', [
        "id" => $attendance->id,
        'type' =>  $attendance->type,
        'date' => $attendance->date,
        'time' =>  $attendance->time
    ]);
});
