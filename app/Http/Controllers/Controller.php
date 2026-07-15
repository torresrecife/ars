<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function legacyTextResponse($content)
    {
        return response((string) $content, 200, array(
            'Content-Type' => 'text/plain; charset=UTF-8',
        ));
    }

    protected function legacyHtmlResponse($content)
    {
        return response((string) $content, 200, array(
            'Content-Type' => 'text/html; charset=ISO-8859-1',
        ));
    }

    protected function legacyJsonResponse($data)
    {
        return response((string) $data, 200, array(
            'Content-Type' => 'application/json; charset=UTF-8',
        ));
    }

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
