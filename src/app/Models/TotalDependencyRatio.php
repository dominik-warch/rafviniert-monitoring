<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

class TotalDependencyRatio extends Model
{
    use HasPostgisColumns;

    protected array $postgisColumns = [
        'geometry' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected $table = 'total_dependency_ratio';

    protected $fillable = [
        'name',
        'value',
        'date_of_dataset',
        'reference_geometry',
        'geometry'
    ];
}
