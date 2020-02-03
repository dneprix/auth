<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Jenssegers\Mongodb\Eloquent\Model;
use Firebase\JWT\JWT;

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

    /**
     * User activation
     *
     * @param string $email_hash
     * @return static
     * @throws \Exception
     */
    public static function activate(string $email_hash): self
    {
        // Get user by email_hash
        $user = self::where('email_hash', '=', $email_hash)->get()->first();
        if (!$user) {
            throw new \Exception(self::ERROR_NOT_FOUND);
        }

        // Activate user
        $user->activated_at = new \DateTime();

        // Save user to db
        $user->save();

        // Return activated user
        return $user;
    }

    /**
     * User authentication
     *
     * @param string $email
     * @param string $password
     * @return string
     * @throws \Exception
     */
    public static function auth(string $email, string $password): string
    {
        // Get user by Email
        $user = self::where('email', '=', strtolower($email))->get()->first();

        // Check user and password hash
        if (!$user || !Hash::check($password, $user->password_hash)) {
            throw new \Exception(self::ERROR_NOT_FOUND);
        }

        // Check activation
        if (!$user->activated_at) {
            throw new \Exception(self::ERROR_NOT_ACTIVATED);
        }

        // Return jwt token
        return JWT::encode([
            'iss' => "auth-jwt",
            'sub' => $user->_id,
            'iat' => time(),
            'exp' => time() + config('jwt.expired')
        ],
            config('jwt.secret')
        );
    }

    /**
     * User registration
     *
     * @param string $email
     * @param string $password
     * @return static
     * @throws \Exception
     */
    public static function register(string $email, string $password): self
    {
        // Create new user
        $user = new self();

        // Set email lowercase
        $user->email = strtolower($email);

        // Set generated password hash
        $user->password_hash = Hash::make($password);

        // Set generated activation hash
        $user->email_hash = bin2hex(random_bytes(32));

        // Set not activated
        $user->activated_at = null;

        // Save user to db
        $user->save();

        // Add activation email to queue
        Email::activation($user);

        // Return user
        return $user;
    }
}
