<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 */
final class User extends Model
{
    const COLLECTION = 'users';
    const ERROR_NOT_FOUND = 'not_found';
    const ERROR_NOT_ACTIVATED = 'not_activated';

    protected $connection = 'mongodb';
    protected $collection = self::COLLECTION;
    protected $primaryKey = '_id';
    protected $fillable = ['email', 'password_hash', 'email_hash', 'activated_at'];
    protected $hidden = ['password_hash', 'email_hash'];
    protected $dates = ['activated_at'];
}
