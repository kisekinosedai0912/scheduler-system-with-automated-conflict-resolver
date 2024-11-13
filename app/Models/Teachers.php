<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teachers extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'teachers';
    protected $fillable = [
        'teacherName',
        'email',
        'contact',
        'numberHours'
    ];
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class, 'teacher_id', 'id');
    }
}
