<?php

use App\Enum\AttendanceType;
use App\Models\Employee;
use App\Models\User;
use App\Service\Report\AttendanceReportService;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);

    $this->employee = Employee::factory()->create([
        'names' => 'Test User',
        'email' => 'test@example.com',
        'employeeIdentifier' => '12345',
        'phoneNumber' => '09876787',
    ]);

    $this->data = [
        "employeeID" => $this->employee->id,
        "type" => AttendanceType::ARRIVE->value,
        "date" => now()->format('Y-m-d'),
        "time" => "06:12"
    ];

    $this->post('/attendance', $this->data);

    $this->service = new AttendanceReportService();
});

test("generate PDF file", function () {
    PDF::fake();

    $response = $this->get("/report/pdf");

    PDF::assertViewIs("report.attendance");
    PDF::assertSeeText($this->employee->names);
    PDF::assertSeeText($this->data['date']);
    PDF::assertSeeText($this->data['time']);
    PDF::assertSeeText('Arrive');

    $this->assertNotEmpty($response->getContent());
    $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    $this->assertEquals('inline; filename="report.pdf"', $response->headers->get('Content-Disposition'));

    $response->assertStatus(200);
});

test("generate Excel file", function () {
    Excel::fake();



    $response = $this->service->excel();

    expect($response)->toBeInstanceOf(AttendanceReportService::class);

    $response = $this->get("/report/excel");

    $response->assertStatus(200);

    Excel::assertDownloaded('report.xlsx', function (AttendanceReportService $export) {

        return $export->array() === [
            [
                "id" => 1,
                "date" => $this->data['date'],
                "names" => $this->employee->names,
                "status" => 'Arrive',
                "time" => $this->data['time']
            ]
        ];
    });
});
