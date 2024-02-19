<?php

namespace App\Service\Report;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Barryvdh\Snappy\PdfWrapper;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceReportService implements FromArray, ShouldAutoSize, WithHeadings, WithEvents
{
  public function pdf(): PdfWrapper
  {

    $pdf = PDF::loadView('report.attendance', [
      'data' => $this->getReportData(),
    ]);

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOption('footer-center', "Generated using Attendance Sysytem.");
    $pdf->setOption('footer-font-size', 10);

    return $pdf;
  }

  public function excel()
  {
    return $this;
  }

  public function array(): array
  {
    $responses = $this->getReportData();

    $finalData = [];

    foreach ($responses['data'] as $key => $response) {
      array_push($finalData, [
        "id" => $key + 1,
        "date" => $response->date,
        "names" => $response->names,
        "status" => $response->status,
        "time" => $response->time
      ]);
    }

    return $finalData;
  }

  public function headings(): array
  {
    return [
      "no",
      "date",
      "names",
      "status",
      "time"
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {

        $sheet = $event->sheet->getDelegate();

        $sheet->getStyle("A1:E1")->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()->setARGB('0000ff');

        $sheet->getStyle("A1:E1")->getFont()
          ->setBold(true)
          ->getColor()
          ->setRGB('white');
      },
    ];
  }

  protected function getReportData(): array
  {
    $date = now()->format('Y-m-d');

    $startDate = Carbon::parse($date)->setHour(0)->setMinute(0)->getTimestamp();

    $endDate = Carbon::parse($date)->addDay()->setHour(0)->setMinute(0)->getTimestamp();

    $data = DB::table('Attendance AS a')
      ->join('Employee AS b', 'a.employeeID', '=', 'b.id')
      ->leftJoin('users AS c', 'a.userID', '=', 'c.id')
      ->where('date', '>=', $startDate)
      ->where('date', '<', $endDate)
      ->select('a.id', DB::raw("FROM_UNIXTIME(a.date, '%Y-%m-%d') AS date"), 'b.names', DB::raw("IF(a.type = 1, 'Arrive', 'Leave') AS status"), 'a.time', 'c.name AS user')
      ->get()->toArray();

    return [
      "data" => $data,
      "day" => $date
    ];
  }
}
