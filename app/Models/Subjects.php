<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;
    protected $table = 'subjects';

    protected $fillable = [
        'category',
        'subjectName',
        'description'
    ];

    public $timestamps = false; 

    
}
