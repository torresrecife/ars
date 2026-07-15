<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidatesLegacyFormRequest
{
	protected function validateLegacyFormRequest(Request $request, $requestClass)
	{
		/** @var FormRequest $formRequest */
		$formRequest = $requestClass::createFrom($request);
		$formRequest->setContainer(app());
		$formRequest->setRedirector(app('redirect'));

		try {
			$formRequest->validateResolved();
			return true;
		} catch (ValidationException $exception) {
			return false;
		}
	}
}
