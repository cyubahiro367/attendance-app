<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Attendance system',
    description: 'Attendance system API Documentation',
    version: '1.0',
    contact: new OA\Contact(
        name: 'Cyubahiro Theotime',
        email: 'theotimecyubahiro@gmail.com'
    )
)]

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
