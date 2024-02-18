<?php

use App\Http\Controllers\EmployeeController;
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

Route::prefix('employee')->middleware('auth')->group(function () {
    Route::get('', [EmployeeController::class, "index"]);
    Route::post('', [EmployeeController::class, "store"]);
    Route::put('{employeeID}', [EmployeeController::class, "update"]);
    Route::delete('{employeeID}', [EmployeeController::class, "destroy"]);
});

require __DIR__ . '/auth.php';
