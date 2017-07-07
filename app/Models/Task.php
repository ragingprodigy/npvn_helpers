<?php
/**
 * Created by PhpStorm.
 * User: dapo
 * Date: 7/7/17
 * Time: 8:26 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const CREATED       = 'IN QUEUE';
    const PROCESSING    = 'PROCESSING';
    const FINISHED      = 'FINISHED';
    const FAILED        = 'FAILED';

    const TABLE_NAME    = 'npvn_user_tasks';

    const ALLOWED_STATUSES = [ self::CREATED, self::PROCESSING, self::FINISHED, self::FAILED ];
    protected $table    = self::TABLE_NAME;

    protected $fillable = [
        'name', 'status'
    ];
}