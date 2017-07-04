<?php
declare(strict_types = 1);

/**
 * @author Oladapo Omonayajo <oladapo.omonayajo@lazada.com.ph>
 * Created on 7/5/2017, 03:44
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalGovernment extends Model
{
    const TABLE_NAME = 'npvn_local_government';
    protected $table = self::TABLE_NAME;

    protected $fillable = ['name'];
}
