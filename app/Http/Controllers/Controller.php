<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function apiJsonResponse($ok, $code, $message, array $data = array(), $status = 200)
    {
        return response()->json(array(
            'ok' => (bool) $ok,
            'code' => (string) $code,
            'message' => (string) $message,
            'data' => $data,
        ), (int) $status);
    }
}
