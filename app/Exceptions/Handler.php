<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle model not found
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aradığınız kayıt bulunamadı.'
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Handle not found
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sayfa bulunamadı.'
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Handle validation
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        });
    }
}
