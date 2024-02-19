<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return [
        "System" => "Attendance System"
    ];
});

Route::prefix('employee')->group(function () {
    Route::get('', [EmployeeController::class, "index"])->name('display-employee');
    Route::post('', [EmployeeController::class, "store"])->name('create-employee');
    Route::put('{employeeID}', [EmployeeController::class, "update"])->name('update-employee');
    Route::delete('{employeeID}', [EmployeeController::class, "destroy"])->name('delete-employee');
});

Route::prefix('attendance')->group(function () {
    Route::get('{from}/{to}', [AttendanceController::class, "index"])->name('display-attendances');
    Route::post('', [AttendanceController::class, "store"])->name('create-attendance');
    Route::put('{attendanceID}', [AttendanceController::class, "update"])->name('update-attendance');
    Route::delete('{attendanceID}', [AttendanceController::class, "destroy"])->name('delete-attendance');
});

Route::get('report/{type}', [ReportController::class, "generate"])->name('generate-pdf');
// });

require __DIR__ . '/auth.php';
