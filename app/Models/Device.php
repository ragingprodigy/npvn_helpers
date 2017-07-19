<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 19/07/2017, 1:40 PM
 */

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Device
 * @package App\Models
 *
 * @method Builder|static       whereImei($value)
 */
class Device extends BaseModel
{
    const TABLE_NAME = 'actual_devices';
    protected $table = self::TABLE_NAME;

    protected $fillable = [
        'available_device_id', 'uuid', 'imei', 'serial', 'added_by', 'updated_by', 'deleted_by',
        'enrolled', 'unbundled', 'allocated', 'dispatched'
    ];

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return BelongsTo
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * @return BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(SelectableDevice::class, 'available_device_id');
    }
}