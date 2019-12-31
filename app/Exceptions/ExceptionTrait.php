<?php
namespace App\Exceptions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
trait ExceptionTrait
{
	public function apiException($request,$exception)
	{
		if ($this->isModel($e)) {
			return $this->ModelResponse($exception);
		}
		if ($this->isHttp($e)) {
			return $this->HttpResponse($exception);
		}
		if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
			$exception = $this->unauthenticated($request, $exception);
		}

		if ($exception instanceof \Illuminate\Validation\ValidationException) {
			$exception = $this->convertValidationExceptionToResponse($exception, $request);
		}
		return $this->customApiResponse($exception);
	}
	protected function isModel($e)
	{
		return $e instanceof ModelNotFoundException;
	}
	protected function isHttp($e)
	{
		return $e instanceof NotFoundHttpException; 
	}
	protected function ModelResponse($e)
	{
		return response()->json([
                    'errors' => 'Product Model not found'
                ],Response::HTTP_NOT_FOUND);
	}
	private function customApiResponse($exception)
	{
		if (method_exists($exception, 'getStatusCode')) {
			$statusCode = $exception->getStatusCode();
		} else {
			$statusCode = 500;
		}

		$response = [];

		switch ($statusCode) {
			case 401:
				$response['message'] = 'Unauthorized';
				break;
			case 403:
				$response['message'] = 'Forbidden';
				break;
			case 404:
				$response['message'] = 'Not Found';
				break;
			case 405:
				$response['message'] = 'Method Not Allowed';
				break;
			case 422:
				$response['message'] = $exception->original['message'];
				$response['errors'] = $exception->original['errors'];
				break;
			default:
				$response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
				break;
		}

		if (config('app.debug')) {
			$response['trace'] = $exception->getTrace();
			$response['code'] = $exception->getCode();
		}

		$response['status'] = $statusCode;

		return response()->json($response, $statusCode);
	}
	protected function HttpResponse($e)
	{
		return response()->json([
                    'errors' => 'Incorect route'
                ],Response::HTTP_NOT_FOUND);
	}
}