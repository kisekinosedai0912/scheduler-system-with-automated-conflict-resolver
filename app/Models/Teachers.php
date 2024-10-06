<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{
    use HasFactory;
    protected $table = 'teachers';
    protected $fillable = [
        'teacherName',
        'categoryName',
        'subject_id',
        'numberHours'
    ];
    public $timestamps = false; 

    public function subject(){
        return $this->belongsTo(Subjects::class, 'subject_id', 'id');
    }
}
