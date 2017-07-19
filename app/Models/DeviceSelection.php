<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/13/17, 4:13 PM
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DeviceSelection
 * @package App\Models
 *
 * @property string     $id
 * @property string     $firstname
 * @property string     $middlename
 * @property string     $surname
 * @property string     $phone
 * @property string     $email
 * @property string     $bvn
 * @property int        $lga_id
 * @property int        $state_id
 * @property Carbon     $selection_date
 */
class DeviceSelection extends BaseModel
{
    const TABLE_NAME = 'device_selection';
    protected $table = self::TABLE_NAME;

    public $timestamps = false;

    protected $dates = [
        'selection_date',
        'date_allocated'
    ];

    protected $casts = [
        'id' => 'string'
    ];

    /**
     * @return BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(SelectableDevice::class, 'available_device_id');
    }

    /**
     * @return BelongsTo
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(Lga::class, 'lga_id');
    }
}
