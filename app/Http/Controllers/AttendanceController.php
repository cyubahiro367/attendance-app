<?php

namespace App\Http\Controllers;

use App\Enum\AttendanceType;
use App\Mail\SendAttendanceRecord;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }
    
    #[OA\Get(
        tags: ['Attendance'],
        path: '/attendance/{from}/{to}',
        description: 'display all attendance',
        summary: 'retrieving all attendance',
    )]
    #[OA\PathParameter(
        name: "from",
        description: "Start date",
        required: true,
        example: "2023-01-01"
    )]
    #[OA\PathParameter(
        name: "to",
        description: "End date",
        required: true,
        example: "2023-12-31"
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully logged in',
        content: new OA\JsonContent(
            example: [
                [
                    "id" => 2,
                    "names" => "First Employee",
                    "email" => "test@test.com",
                    "employeeIdentifier" => "123456",
                    "phoneNumber" => "125465",
                    "status" => "Arrive",
                    "user" => null
                ]
            ]
        )
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
    /**
     * Display a listing of the resource.
     */
    public function index(string $from, string $to)
    {
        $startDate = Carbon::parse($from)->setHour(0)->setMinute(0)->getTimestamp();

        $endDate = Carbon::parse($to)->addDay()->setHour(0)->setMinute(0)->getTimestamp();

        $attendance = DB::table('Attendance AS a')
            ->join('Employee AS b', 'a.employeeID', '=', 'b.id')
            ->leftJoin('users AS c', 'a.userID', '=', 'c.id')
            ->where('date', '>=', $startDate)
            ->where('date', '<', $endDate)
            ->select('a.id', 'b.names', DB::raw("FROM_UNIXTIME(a.date, '%Y-%m-%d') AS date"), 'a.time', DB::raw("IF(a.type = 1, 'Arrive', 'Leave') AS status"), 'c.name AS user')
            ->get();

        return response()->json($attendance);
    }

    #[OA\Post(
        tags: ['Attendance'],
        path: '/attendance',
        description: 'create attendance',
        summary: 'Create attendance',
    )]
    #[OA\RequestBody(
        description: "Payload for updating a company role",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "employeeID",
                    format: "integer",
                    example: 1
                ),
                new OA\Property(
                    property: "type",
                    format: "integer",
                    example: 1
                ),
                new OA\Property(
                    property: "date",
                    format: "string",
                    example: "2023-01-01"
                ),
                new OA\Property(
                    property: "time",
                    format: "string",
                    example: "06:12"
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully created',
        content: new OA\JsonContent(
            example: [
                "message" => "Attendance created successfully"
            ]
        )
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
        response: 422,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            example: [
                "message" => "Attendance not found"
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employeeID' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'integer', 'in:1,2'],
            'date' => ['required', 'date'],
            'time' => ['required', "date_format:H:i"],
        ]);

        $employee = Employee::where('id', $request->employeeID)->first();

        throw_if(is_null($employee), Exception::class, "Employee not found");

        $date = Carbon::parse($request->date)->setHour(0)->setMinute(0)->getTimestamp();

        $existArrival = Attendance::where([['employeeID', $employee->id], ['date', $date], ['type', $request->type]])->exists();

        throw_if($existArrival, Exception::class, "Attendance recorded");

        if ($request->type === AttendanceType::LEAVE->value) {
            $leaveBefore = Attendance::where([['employeeID', $employee->id], ['date', $date], ['type', AttendanceType::ARRIVE->value]])->first();
            throw_if(is_null($leaveBefore), Exception::class, "Record Arrival first");
        }

        DB::transaction(function () use($employee, $request, $date){
            Attendance::firstorCreate([
                'employeeID' => $employee->id,
                'type' => $request->type,
                'date' => $date,
            ], [
                'employeeID' => $employee->id,
                'type' => $request->type,
                'date' => $date,
                'time' => $request->time,
                'userID' => Auth::id()
            ]);

            Mail::to($employee->email)->send(new SendAttendanceRecord($employee->names));
        });

        return response()->json(["message" => "Attendance recorded successfully"]);
    }

    #[OA\Put(
        tags: ['Attendance'],
        path: '/attendance/{attendanceID}',
        description: 'Update attendance',
        summary: 'Update attendance',
    )]
    #[OA\PathParameter(
        name: "attendanceID",
        description: "ID of the attendance to update",
        required: true,
        example: 1
    )]
    #[OA\RequestBody(
        description: "Payload for updating a company role",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "employeeID",
                    format: "integer",
                    example: 1
                ),
                new OA\Property(
                    property: "type",
                    format: "integer",
                    example: 1
                ),
                new OA\Property(
                    property: "date",
                    format: "string",
                    example: "2023-01-01"
                ),
                new OA\Property(
                    property: "time",
                    format: "string",
                    example: "06:12"
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully updated',
        content: new OA\JsonContent(
            example: [
                "message" => "Attendance Updated successfully"
            ]
        )
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
        response: 422,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            example: [
                "message" => "Attendance not found"
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
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $attendanceID)
    {
        $request->validate([
            'employeeID' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'integer', 'in:1,2'],
            'date' => ['required', 'date'],
            'time' => ['required', "date_format:H:i"],
        ]);

        $attendance = Attendance::where('id', $attendanceID)->first();

        throw_if(is_null($attendance), Exception::class, "Attendance not found");

        $date = Carbon::parse($request->date)->setHour(0)->setMinute(0)->getTimestamp();

        if ($request->type === AttendanceType::LEAVE->value) {

            $leaveBefore = Attendance::where([['employeeID', $request->employeeID], ['date', $date], ['type', AttendanceType::ARRIVE->value]])->first();

            throw_if(is_null($leaveBefore), Exception::class, "Record Arrival first");
        }

        $attendance->update([
            'employeeID' => $request->employeeID,
            'type' => $request->type,
            'date' => $date,
            'time' => $request->time,
        ]);

        return response()->json(["message" => "Attendance updatted successfully"]);
    }

    #[OA\Delete(
        tags: ['Attendance'],
        path: '/attendance/{attendanceID}',
        description: 'Delete attendance',
        summary: 'Delete attendance',
    )]
    #[OA\PathParameter(
        name: "attendanceID",
        description: "ID of the attendance to delete",
        required: true,
        example: 1
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully Deleted',
        content: new OA\JsonContent(
            example: [
                "message" => "Attendance deleted successfully"
            ]
        )
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
        response: 404,
        description: 'Unauthenticated',
        content: new OA\JsonContent(
            example: [
                "message" => "Attendance not found"
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $attendanceID)
    {
        $attendance = Attendance::where('id', $attendanceID)->first();

        throw_if(is_null($attendance), Exception::class, "Attendance not found");

        $attendance->delete();

        return response()->json(["message" => "Attendance deleted successfully"]);
    }
}
