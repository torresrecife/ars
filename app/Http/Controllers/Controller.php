<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function mapWriteResultToJson($result, $successMessage, $duplicateMessage = null, $errorMessage = null)
    {
        if ((string) $result === '1') {
            return $this->apiJsonResponse(true, 'success', (string) $successMessage);
        }
        if ((string) $result === '2') {
            return $this->apiJsonResponse(false, 'duplicate', (string) ($duplicateMessage ?: __('Duplicate record.')), array(), 409);
        }

        return $this->apiJsonResponse(false, 'error', (string) ($errorMessage ?: __('Operation failed.')), array(), 500);
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
