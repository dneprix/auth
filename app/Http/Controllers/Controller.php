<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{

    /**
     * Success response
     *
     * @param null $message
     * @return JsonResponse
     */
    protected function success($message = null): JsonResponse
    {
        // Set success response
        $response = ['success' => true];

        // Add message if exists
        if ($message) {
            $response['message'] = $message;
        }

        // Return json response
        return response()->json($response);
    }
}
