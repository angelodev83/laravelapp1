<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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

    public function render($request, Throwable $exception)
    {
        if( $request->isMethod('get') )
        {
            if($exception->getMessage() == 'Unauthenticated.')
            {
                return parent::render($request, $exception);
            }

            if (method_exists($exception, 'getStatusCode')) {
                if ($exception->getStatusCode() === 403) {
                    if($request->route()->getActionName() == '\App\Http\Controllers\DashboardController@index') {
                        return redirect('/lost');
                    };
                    return response()->view('/errors/403/index', [], 403);
                }

                if ($exception->getStatusCode() === 404) {
                    return redirect('/admin');
                    // return response()->view('/errors/404/index', [], 404);
                }
            }
        }

        if ($exception instanceof TokenMismatchException) {
            // Return a custom response or redirect
            return redirect()
                ->back()
                ->withInput($request->except('_token'))
                ->with('error', 'Your session has expired. Please try again.');
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return redirect()->guest(route('login'))->with('error', 'You must be logged in to access that page.');
        }

        if ($exception instanceof AuthenticationException) {
            return redirect()->guest(route('login'))->with('error', 'You must be logged in to access that page.');
        }

        return parent::render($request, $exception);
    }
}
