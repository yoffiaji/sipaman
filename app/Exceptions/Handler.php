<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // Tangani unauthenticated — kembalikan JSON 401
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
        ], 401);
    }

    // Tangani semua exception — selalu kembalikan JSON untuk API
    public function render($request, Throwable $e)
    {
        // Model tidak ditemukan → 404 JSON
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        // Validasi gagal → 422 JSON
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);
        }

        return parent::render($request, $e);
    }
}