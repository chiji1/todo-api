<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use App\Helpers\Utilities;
use Asm89\Stack\CorsService;
use HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse, Utilities;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        AuthenticationException::class,
        ValidationException::class,
        ModelNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $response = $this->handleExceptions($request, $e);

//        app(CorsService::class)->addActualRequestHeaders($response, $request);

        return $response;
    }

    public function handleExceptions($request, Throwable $exception) {
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }
        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->sendError("{$modelName} with defined id does not exist", [], Response::HTTP_NOT_FOUND);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof AuthorizationException) {
            return $this->sendError($exception->getMessage(), [], Response::HTTP_FORBIDDEN);
        }
        if ($exception instanceof NotFoundHttpException) {
            return $this->sendError("The URL you're looking for does not exist", [], Response::HTTP_NOT_FOUND);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->sendError('The specified method for the request is invalid', [], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        if ($exception instanceof HttpException) {
            return $this->sendError($exception->getMessage(), [], $exception->getStatusCode());
        }

//        if ($exception instanceof TokenExpiredException) {
//            return $this->sendError('token expired', [],  Response::HTTP_BAD_REQUEST);
//        } else if ($exception instanceof TokenInvalidException) {
//            return $this->sendError('invalid token', [], Response::HTTP_BAD_REQUEST);
//        } else if ($exception instanceof JWTException) {
//            return $this->sendError($exception->getMessage(), [], Response::HTTP_BAD_REQUEST);
//        }
        if ($exception instanceof QueryException) {
            $error_code = $exception->errorInfo[1];
            if ($error_code == 1451) {
                return $this->sendError('Cannot delete this resource permanently, it is related to another resource', [], Response::HTTP_CONFLICT);
            }
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->sendError('Something Unexpected has occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        $errorValues = $this->array_flatten($errors);
        return $this->sendError('Validation Error', $errorValues, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
