<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'Employee';

    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['names', 'email', 'employeeIdentifier', 'phoneNumber'];
}
