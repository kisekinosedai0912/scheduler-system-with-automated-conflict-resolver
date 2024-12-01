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
        'semester',
        'categoryName',
        'subject_id',
        // 'studentNum',
        'year',
        'section',
        'days',
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

    public function getIsConflictedAttribute() {
        return $this->hasConflict();
    }

    // Private function exclusive only to the schedules model, this function will handle the checking of the conflicted schedules
    public function hasConflict() {
        // Split the days of the current schedule and convert to array
        $currentScheduleDays = explode('-', $this->days);

        // Find conflicting schedules
        return Schedules::where('id', '!=', $this->id)
            ->where(function($query) {
                // Time overlap condition
                $query->whereBetween('startTime', [$this->startTime, $this->endTime])
                      ->orWhereBetween('endTime', [$this->startTime, $this->endTime])
                      ->orWhere(function($query) {
                          $query->where('startTime', '<=', $this->startTime)
                                ->where('endTime', '>=', $this->endTime);
                      });
            })
            ->where(function($query) use ($currentScheduleDays) {
                // Check for days that overlap
                $query->where(function($subQuery) use ($currentScheduleDays) {
                    foreach ($currentScheduleDays as $day) {
                        $subQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                    }
                });
            })
            ->where(function($query) {
                // Conflict scenarios
                $query->where(function($subQuery) {
                    // Scenario 1: Same teacher, same scheduled time & day (even in different sections)
                    $subQuery->where('teacher_id', $this->teacher_id);
                })
                ->orWhere(function($subQuery) {
                    // Scenario 2: Same teacher, same scheduled time, day & section
                    $subQuery->where('teacher_id', $this->teacher_id)
                             ->where('section', $this->section);
                })
                ->orWhere(function($subQuery) {
                    // Scenario 3: Different teachers, same scheduled time, day and section
                    $subQuery->where('section', $this->section)
                             ->where('teacher_id', '!=', $this->teacher_id);
                });
            })
            ->exists();
    }

    public function calculateAndUpdateTeacherHours() {
        $startTime = \Carbon\Carbon::parse($this->startTime);
        $endTime = \Carbon\Carbon::parse($this->endTime);
        $duration = $startTime->diffInHours($endTime, true);

        // Find the teacher and update their total loaded hours based on the schedule provided
        $teacher = $this->teacher;
        if ($teacher) {
            $teacher->numberHours = max(0, $teacher->numberHours + $duration); // Preventing negative hours
            $teacher->save(); // Save the updated hours
        }
    }
}
