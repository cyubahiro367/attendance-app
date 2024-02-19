<?php

namespace App\Http\Controllers;

use App\Service\Report\AttendanceReportService;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    public function __construct(public AttendanceReportService $report)
    {
        $this->middleware(['auth:sanctum']);
    }

    #[OA\Get(
        tags: ['Report'],
        path: '/report/{type}',
        description: 'generate Daily Report',
        summary: 'retrieving all employees',
    )]
    #[OA\PathParameter(
        name: "type",
        description: "pdf of excel",
        required: true,
        example: "pdf"
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully logged in',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            example: [
                "message" => "Unauthenticated."
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
    public function generate(string $type)
    {
        return match ($type) {
            'pdf' => $this->report->pdf()->inline('report.pdf'),
            'excel' => Excel::download($this->report->excel(), 'report.xlsx'),
        };
    }
}
