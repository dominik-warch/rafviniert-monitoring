<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static distinct(string $string)
 * @method static where(string $string, string $referenceGeometry)
 */
class ReferenceGeometry extends Model
{
    use HasPostgisColumns;

    protected array $postgisColumns = [
        'geometry' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected $table = 'reference_geometries';

    protected $fillable = [
        'name',
        'geometry'
    ];
}
