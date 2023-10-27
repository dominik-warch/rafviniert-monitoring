<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasPostgisColumns;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zip_code',
        'city',
        'street',
        'housenumber',
        'housenumber_ext',
        'geometry'
    ];

    /**
     * The spatial fields to be processed by PostGIS.
     *
     * @var array
     */
    protected array $postgisColumns = [
        'geometry' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    /**
     * Get the full address as a string.
     *
     * @return string
     */
    public function toAddressString()
    {
        $addressParts = [
            $this->street,
            $this->housenumber . ($this->housenumber_ext ? ' ' . $this->housenumber_ext : ''),
            $this->zip_code,
            $this->city,
        ];

        return implode(', ', array_filter($addressParts));
    }
}
