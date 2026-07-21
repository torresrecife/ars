<?php

namespace App\Http\Controllers;

use App\Support\WriteResult;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function mapWriteResultToJson(WriteResult $result, $successMessage, $duplicateMessage = null, $errorMessage = null, $linkedUsersMessage = null)
    {
        if ($result->isSuccess()) {
            return $this->apiJsonResponse(true, 'success', (string) $successMessage);
        }
        if ($result->isDuplicate()) {
            return $this->apiJsonResponse(false, 'duplicate', (string) ($duplicateMessage ?: __('Duplicate record.')), array(), 409);
        }
        if ($result->isLinkedUsers()) {
            return $this->apiJsonResponse(false, 'linked_users', (string) ($linkedUsersMessage ?: __('There are linked users.')), array(), 409);
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
