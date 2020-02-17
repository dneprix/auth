<?php

use App\Models\User;
use \Illuminate\Support\Facades\Hash;
use App\Services\UserService;

class UserServiceTest extends TestCase
{
    const TEST_EMAIL = 'test@email.com';
    const TEST_PASSWORD = 'test_password';
    const TEST_EMAIL_HASH = 'test_email_hash';

    public function testAuthSuccess()
    {
        // Create activated user in db
        $user = new User();
        $user->email = self::TEST_EMAIL;
        $user->password_hash = Hash::make(self::TEST_PASSWORD);
        $user->activated_at = new \DateTime();
        $user->save();

        // Auth user
        $userService = new UserService();
        $token = $userService->auth(self::TEST_EMAIL, self::TEST_PASSWORD);

        // Validate Token
        $this->assertNotEmpty($token);
    }

    public function testAuthFailPassword()
    {
        // Created activated user in db
        $user = new User();
        $user->email = self::TEST_EMAIL;
        $user->password_hash = Hash::make(self::TEST_PASSWORD);
        $user->activated_at = new \DateTime();
        $user->save();

        // Expect not found exception
        $this->expectExceptionMessage(User::ERROR_NOT_FOUND);

        // Try to auth user with wrong pass
        $userService = new UserService();
        $token = $userService->auth(self::TEST_EMAIL, 'wrong_password');

        // Validate Empty Token
        $this->assertEmpty($token);
    }

    public function testAuthFailActivation()
    {
        // Created not activated user in db
        $user = new User();
        $user->email = self::TEST_EMAIL;
        $user->password_hash = Hash::make(self::TEST_PASSWORD);
        $user->activated_at = null;
        $user->save();

        // Expect not found exception
        $this->expectExceptionMessage(User::ERROR_NOT_ACTIVATED);

        // Try to auth user with wrong pass
        $userService = new UserService();
        $token = $userService->auth(self::TEST_EMAIL, self::TEST_PASSWORD);

        // Validate Token
        $this->assertNotEmpty($token);
    }


    public function testActivateSuccess()
    {
        // Created user in db
        $user = new User();
        $user->email = self::TEST_EMAIL;
        $user->email_hash = self::TEST_EMAIL_HASH;
        $user->save();

        // User activation
        $userService = new UserService();
        $res = $userService->activate(self::TEST_EMAIL_HASH);

        // Check activated user in db
        $this->seeInDatabase(User::COLLECTION, ['email' => self::TEST_EMAIL, 'activated_at' => $res->activated_at]);
    }

    public function testActivateFail()
    {
        // Created user in db
        $user = new User();
        $user->email = self::TEST_EMAIL;
        $user->email_hash = self::TEST_EMAIL_HASH;
        $user->save();

        // Expect hash not found exception
        $this->expectExceptionMessage(User::ERROR_NOT_FOUND);

        // Try to activate wrong hash
        $userService = new UserService();
        $userService->activate('wrong_hash');

        // Check user not activated
        $this->seeInDatabase(User::COLLECTION, ['email' => self::TEST_EMAIL, 'activated_at' => null]);
    }

    public function testRegisterSuccess()
    {
        // Check no user in db
        $this->notSeeInDatabase(User::COLLECTION, ['email' => self::TEST_EMAIL]);

        // Register user
        $userService = new UserService();
        $userService->register(self::TEST_EMAIL, self::TEST_PASSWORD);

        // Check created user in db ant not activated
        $this->seeInDatabase(User::COLLECTION, ['email' => self::TEST_EMAIL, 'activated_at' => null]);
    }

    public function testRegisterFail()
    {
        // Created user in db
        $user = new User();
        $user->email = self::TEST_EMAIL;
        $user->save();

        // Expect duplicate user exception
        $this->expectExceptionMessage('duplicate key error collection');

        // Try to register duplicate user
        $userService = new UserService();
        $userService->register(self::TEST_EMAIL, self::TEST_PASSWORD);
    }

}
