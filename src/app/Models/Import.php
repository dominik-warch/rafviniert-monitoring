<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $table = 'imports';

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
