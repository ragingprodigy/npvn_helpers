<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/13/17, 4:32 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends BaseModel
{
    const TABLE_NAME = 'states';
    protected $table = self::TABLE_NAME;

    /**
     * @return HasMany
     */
    public function lgas(): HasMany
    {
        return $this->hasMany('', 'state_id');
    }

    /**
     * @return HasMany
     */
    public function selections(): HasMany
    {
        return $this->hasMany(DeviceSelection::class, 'lga_id');
    }
}