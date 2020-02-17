<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    /**
     * UserController constructor.
     *
     * @param UserService $user
     */
    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    /**
     * Activation user handler
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function activate(Request $request): JsonResponse
    {
        // Validate request
        $this->validate($request, [
            'email_hash' => 'required|exists:users',
        ]);

        // Activate user
        $this->user->activate(
            $request->get('email_hash')
        );

        // Success response
        return $this->success();
    }

    /**
     * Authentication user handler
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function auth(Request $request): JsonResponse
    {
        // Validate request
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Auth user
        $token = $this->user->auth(
            $request->get('email'),
            $request->get('password')
        );

        // Success response token
        return $this->success($token);
    }

    /**
     * Registration user handler
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request, EmailService $email): JsonResponse
    {
        // Validate request
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Register user
        $user = $this->user->register(
            $request->get('email'),
            $request->get('password')
        );

        // Activation email
        $email->activation($user);

        // Success response user
        return $this->success($user);
    }
}
