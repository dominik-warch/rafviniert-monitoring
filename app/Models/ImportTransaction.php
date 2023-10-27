<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportTransaction extends Model
{
    use HasFactory;

    protected $table = 'imports_transaction';

    protected $fillable = [
        'file_path',
        'file_name',
        'dataset_date',
        'column_mapping',
        'transaction_type',
    ];

    protected $casts = [
        'column_mapping' => 'array',
    ];
}
