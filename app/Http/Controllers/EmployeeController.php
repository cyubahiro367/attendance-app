<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
    }

    #[OA\Get(
        tags: ['Employee'],
        path: '/employee',
        description: 'display all employees',
        summary: 'retrieving all employees',
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
                    "phoneNumber" => "125465"
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
    public function index()
    {
        $employees = Employee::all(['id', 'names', 'email', 'employeeIdentifier', 'phoneNumber']);

        return response()->json($employees);
    }

    #[OA\Post(
        tags: ['Employee'],
        path: '/employee',
        description: 'Creating new employee',
        summary: 'Create new employee',
    )]
    #[OA\RequestBody(
        description: "Payload for updating a company role",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "names",
                    format: "string",
                    example: "First Employee"
                ),
                new OA\Property(
                    property: "email",
                    format: "string",
                    example: "test@test.com"
                ),
                new OA\Property(
                    property: "employeeIdentifier",
                    format: "string",
                    example: "12000800973456"
                ),
                new OA\Property(
                    property: "phoneNumber",
                    format: "string",
                    example: "+2507867625739"
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully logged in',
        content: new OA\JsonContent(
            example: [
                "message" => "Employee created successfully"
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
                "message" => "The email has already been taken. (and 2 more errors)",
                "errors" => [
                    "email" => [
                        "The email has already been taken."
                    ],
                    "employeeIdentifier" => [
                        "The employee identifier has already been taken."
                    ],
                    "phoneNumber" => [
                        "The phone number has already been taken."
                    ]
                ]
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
            'names' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Employee::class],
            'employeeIdentifier' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . Employee::class],
            'phoneNumber' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . Employee::class],
        ]);

        Employee::firstOrCreate([
            'email' => $request->email
        ], [
            'names' => $request->names,
            'email' => $request->email,
            'employeeIdentifier' => $request->employeeIdentifier,
            'phoneNumber' => $request->phoneNumber,
        ]);

        return response()->json(["message" => "Employee created successfully"]);
    }

    #[OA\Put(
        tags: ['Employee'],
        path: '/employee/{employeeID}',
        description: 'Update employees',
        summary: 'Update employees',
    )]
    #[OA\PathParameter(
        name: "employeeID",
        description: "ID of the employee to update",
        required: true,
        example: 1
    )]
    #[OA\RequestBody(
        description: "Payload for updating a company role",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "names",
                    format: "string",
                    example: "First Employee"
                ),
                new OA\Property(
                    property: "email",
                    format: "string",
                    example: "test@test.com"
                ),
                new OA\Property(
                    property: "employeeIdentifier",
                    format: "string",
                    example: "12000800973456"
                ),
                new OA\Property(
                    property: "phoneNumber",
                    format: "string",
                    example: "+2507867625739"
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully updated',
        content: new OA\JsonContent(
            example: [
                "message" => "Employee Updated successfully"
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
                "message" => "The email has already been taken. (and 2 more errors)",
                "errors" => [
                    "email" => [
                        "The email has already been taken."
                    ],
                    "employeeIdentifier" => [
                        "The employee identifier has already been taken."
                    ],
                    "phoneNumber" => [
                        "The phone number has already been taken."
                    ]
                ]
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
    public function update(Request $request, int $employeeID)
    {
        $request->validate([
            'names' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'employeeIdentifier' => ['required', 'string', 'lowercase', 'max:255'],
            'phoneNumber' => ['required', 'string', 'lowercase', 'max:255'],
        ]);

        $employee = Employee::where('id', $employeeID)->first();

        throw_if(is_null($employee), Exception::class, "Employee not found");

        $isEmployeeEmailExists = Employee::where([['id', '!=', $employee->id], ['email', $request->email]])->exists();

        throw_if($isEmployeeEmailExists, Exception::class, "Email already in use!");

        $isEmployeeEmployeeIdentifierExists = Employee::where([['id', '!=', $employee->id], ['employeeIdentifier', $request->employeeIdentifier]])->exists();

        throw_if($isEmployeeEmployeeIdentifierExists, Exception::class, "employee Identifier already in use!");

        $isEmployeephoneNumberExists = Employee::where([['id', '!=', $employee->id], ['phoneNumber', $request->phoneNumber]])->exists();

        throw_if($isEmployeephoneNumberExists, Exception::class, "phoneNumber already in use!");

        $employee->update([
            'names' => $request->names,
            'email' => $request->email,
            'employeeIdentifier' => $request->employeeIdentifier,
            'phoneNumber' => $request->phoneNumber,
        ]);

        return response()->json(["message" => "Employee updatted successfully"]);
    }

    #[OA\Delete(
        tags: ['Employee'],
        path: '/employee/{employeeID}',
        description: 'Update employees',
        summary: 'Update employees',
    )]
    #[OA\PathParameter(
        name: "employeeID",
        description: "ID of the employee to delete",
        required: true,
        example: 1
    )]
    #[OA\Response(
        response: 204,
        description: 'Successfully deleted',
        content: new OA\JsonContent(
            example: [
                "message" => "Employee deleted successfully"
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
                "message" => "Employee not found"
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
    public function destroy(int $employeeID)
    {
        $employee = Employee::where('id', $employeeID)->first();

        throw_if(is_null($employee), Exception::class, "Employee not found");

        $employee->delete();

        return response()->json(["message" => "Employee deleted successfully"]);
    }
}
