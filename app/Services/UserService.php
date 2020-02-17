<?php

namespace App\Services;

use App\Models\Email;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class UserService
{

    /**
     * User activation
     *
     * @param string $email_hash
     * @return static
     * @throws \Exception
     */
    public function activate(string $email_hash): User
    {
        // Get user by email_hash
        $user = User::where('email_hash', '=', $email_hash)->get()->first();
        if (!$user) {
            throw new \Exception(User::ERROR_NOT_FOUND);
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
    public function auth(string $email, string $password): string
    {
        // Get user by Email
        $user = User::where('email', '=', strtolower($email))->get()->first();

        // Check user and password hash
        if (!$user || !Hash::check($password, $user->password_hash)) {
            throw new \Exception(User::ERROR_NOT_FOUND);
        }

        // Check activation
        if (!$user->activated_at) {
            throw new \Exception(User::ERROR_NOT_ACTIVATED);
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
    public function register(string $email, string $password): User
    {
        // Create new user
        $user = new User();

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

        // Return user
        return $user;
    }
}
