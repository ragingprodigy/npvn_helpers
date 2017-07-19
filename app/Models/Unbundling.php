<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 19/07/2017, 3:41 PM
 */

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Class Unbundling
 * @package App\Models
 */
class Unbundling extends BaseModel
{
    const TABLE_NAME = "unbundling";
    protected $table = self::TABLE_NAME;

    protected $casts = [
        'power' => 'boolean',
        'accessories' => 'boolean',
        'assessment' => 'boolean',
    ];

    protected $fillable = [
        'power', 'accessories', 'assessment', 'certified_by'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'certified_by');
    }
}