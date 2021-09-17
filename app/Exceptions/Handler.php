<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Throwable;
use Adldap\Auth\BindException as BindException;

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
        //
        /*$this->renderable(function (Exceptions $e, $request) {
            dd($e);
            if($e instanceof BindException) {
                return response('The specified URL cannot be  found.', 404);
            }
        });*/
        /*$this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->json([
                'message' => 'You do not have the required authorization.'
            ], 403);
        });*/
    }

    public function report(Throwable $e)
    {
        //
    }

    public function render($request, Throwable $e){
        if ($e instanceof BindException){
            return response()->view('errors.500.adldap', [], 500);
        }
        if ($e instanceof MethodNotAllowedHttpException || $e instanceof RouteNotFoundException) {
            return response()->view('errors.404', [], 404);
            //return "ADLDAP error";
        }
        
        return parent::render($request, $e);
    }
}
