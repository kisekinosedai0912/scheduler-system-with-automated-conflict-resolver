<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;
    protected $table = 'schedules';

    protected $fillable = [
        'teacherName',
        'subject',
        'studentNum',
        'yearSection',
        'room',
        'startTime',
        'endTime'
    ];
    public $timestamps = false; 
}
