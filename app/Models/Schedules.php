<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedules extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'schedules';

    protected $fillable = [
        'teacher_id',
        'categoryName',
        'subject_id',
        'studentNum',
        'yearSection',
        'room_id',
        'startTime',
        'endTime'
    ];
    public $timestamps = false; 

    public function teacher() {
        return  $this->belongsTo(Teachers::class, 'teacher_id', 'id')->withTrashed();
    }
    public function subject() {
        return  $this->belongsTo(Subjects::class, 'subject_id', 'id');
    }
    public function classroom() {
        return   $this->belongsTo(Classroom::class, 'room_id', 'id');
    }
}
