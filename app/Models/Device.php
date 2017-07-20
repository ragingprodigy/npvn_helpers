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
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected $dates = [
        'date_enrolled'
    ];

    protected $fillable = [
        'available_device_id', 'uuid', 'imei', 'serial', 'added_by', 'updated_by', 'deleted_by',
        'enrolled', 'unbundled', 'allocated', 'dispatched', 'misdn', 'date_enrolled',
        'enrolled_by'
    ];

    protected $casts = [
        'allocated' => 'boolean',
        'enrolled' => 'boolean',
        'unbundled' => 'boolean',
        'dispatched' => 'boolean',
        '_allocated' => 'integer',
        '_enrolled' => 'integer',
        '_unbundled' => 'integer',
        '_dispatched' => 'integer',
    ];

    /**
     * @param $data
     * @return static
     */
    public static function byIMEI($data)
    {
        return static::where('imei', $data)->first();
    }

    /**
     * @return HasOne
     */
    public function volunteer(): HasOne
    {
        return $this->hasOne(DeviceSelection::class, 'actual_device_id');
    }

    public function enroller()
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

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

    /**
     * @return HasOne
     */
    public function unbundling(): HasOne
    {
        return $this->hasOne(Unbundling::class, 'actual_device_id');
    }
}