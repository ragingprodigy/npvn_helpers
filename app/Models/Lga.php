<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/13/17, 4:33 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lga extends BaseModel
{
    const TABLE_NAME = 'lgas';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * @return HasMany
     */
    public function selections(): HasMany
    {
        return $this->hasMany(DeviceSelection::class, 'lga_id');
    }
}