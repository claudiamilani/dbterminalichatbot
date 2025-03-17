<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response|RedirectResponse
    {
        if ($e instanceof PostTooLargeException) {
            if ($this->shouldReturnJson($request, $e)) {
                return response()->json(['msg' => trans('validation.upload_max_file_size', ['size' => ini_get('upload_max_filesize')])]);
            }
        }
        return parent::render($request, $e);
    }
}
