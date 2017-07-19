<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 19/07/2017, 1:40 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;

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
}