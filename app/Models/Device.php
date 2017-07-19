<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/13/17, 4:29 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Device
 * @package App\Models
 * @property int    $id
 * @property string $name
 */
class Device extends BaseModel
{
    const TABLE_NAME = 'available_devices';
    protected $table = self::TABLE_NAME;

    /**
     * @return HasMany
     */
    public function selections(): HasMany
    {
        return $this->hasMany(DeviceSelection::class, 'available_device_id');
    }
}