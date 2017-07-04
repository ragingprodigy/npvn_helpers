<?php
declare(strict_types = 1);

/**
 * @author Oladapo Omonayajo <oladapo.omonayajo@lazada.com.ph>
 * Created on 7/5/2017, 03:41
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionCenter extends Model
{
    const TABLE_NAME = 'npvn_collection_center';
    protected $table = self::TABLE_NAME;

    protected $fillable = ['address', 'geocoded_address'];

    /**
     * @return BelongsTo
     */
    public function lga(): BelongsTo
    {
        return $this->belongsTo(LocalGovernment::class, 'lga_id');
    }
}
