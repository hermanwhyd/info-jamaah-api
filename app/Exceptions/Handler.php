<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'errors' => [
                    'Unauthenticated'
                ]
            ], 401);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'This action is unauthorized.',
                'errors' => [
                    'This action is unauthorized.'
                ]
            ], 403);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Entry for ' . str_replace('App\\Model\\', '', $exception->getModel()) . ' not found',
                'errors' => [
                    'Entry for ' . str_replace('App\\Model\\', '', $exception->getModel()) . ' not found'
                ]
            ], 404);
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'The given data was invalid.',
                'errors' => collect($exception->errors())->flatten()
            ], 422);
        }

        if ($exception instanceof QueryException) {

            $httpCode = 400;
            if ($exception->getCode() === "23000") {
                $httpCode = 409;
            }

            return response()->json([
                'status' => 'error',
                'message' => $exception->errorInfo[2],
                'errors' => config('app.debug') ? [$exception->getMessage()] : []
            ], $httpCode);
        }

        return response()->json([
            'status' => 'error',
            'message' => $exception->getMessage(),
            'errors' => config('app.debug') ? [$exception->getMessage()] : []
        ], 500);
    }
}
