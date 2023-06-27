<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'file_name',
        'dataset_date',
        'column_mapping',
    ];

    protected $casts = [
        'column_mapping' => 'array',
    ];
}
