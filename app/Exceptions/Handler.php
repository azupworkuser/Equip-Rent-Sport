<?php

namespace App\Exceptions;

use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;
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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (
            ($exception instanceof TenantDatabaseDoesNotExistException) ||
            (tenant() && (!tenant('ready')) && $exception instanceof QueryException) ||
            (tenant() && (!tenant('ready')) && $exception instanceof ViewException && $exception->getPrevious() instanceof QueryException)
        ) {
            return response()->view('errors.building');
        }

        if ($exception instanceof TenantCouldNotBeIdentifiedException) {
            // return redirect()->route('tenant.app');
            Log::error('Tenant could not be identified');
        }

        return parent::render($request, $exception);
    }
}
