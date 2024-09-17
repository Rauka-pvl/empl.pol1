<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabGrade extends Model
{
    use HasFactory;
    protected $table = 'kab_grades';
    protected $guarded = [];
}
