<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class Email
 * @package App\Models
 */
final class Email extends Model
{
    const COLLECTION = 'emails';

    protected $connection = 'mongodb';
    protected $collection = self::COLLECTION;
    protected $primaryKey = '_id';
    protected $fillable = ['user_id', 'type', 'status'];

    const STATUS_NEW = 'new';
    const STATUS_SENDING = 'sending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    const TYPE_ACTIVATION = 'activation';

}

