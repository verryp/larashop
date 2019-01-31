<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

// Sertakan lib buat manggil expnya
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

//Lib Catch error buat autentikasi
use Illuminate\Auth\AuthenticationException;

use Illuminate\Support\Facades\Route;
use Auth;

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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // read konfigurasi mode aplikasi apakah prod mode atau dev mode
        $debug = config('app.debug');
        // $web = base_path('routes/web.php');
        // $api = base_path('routes/api.php');
        $message = '';
        $status_code = 500;
        
        if ($exception instanceof ModelNotFoundException) {
            $message = 'Model tidak ditemukan';
            $status_code = 404;
        } elseif ($exception instanceof NotFoundHttpException) {
            $message = 'Sumber tidak ditemukan';
            $status_code = 404;
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $message = 'Method tidak ditemukan';
            $status_code = 405;
        } elseif ($exception instanceof ValidationException) {
            $validationErrors = $exception->validator->errors()->getMessages();

            $validationErrors = array_map(function($error) {
                return array_map(function($message) {
                    return $message;
                }, $error);
            }, $validationErrors);

            $message = $validationErrors;
            $status_code = 405;
        } elseif ($exception instanceof QueryException) {

            if ($debug) {
                $message = $exception->getMessage();
            } else {
                $message = 'Query gagal di eksekusi';
            }
            $status_code = 500;
        }

        $rendered = parent::render($request, $exception);
        $status_code = $rendered->getStatusCode();

        if ( empty($message) ) {
            $message = $exception->getMessage();
        }

        $errors = [];

        if ($debug) {
            $errors['exception'] = get_class($exception);
            $errors['trace'] = explode("\n", $exception->getTraceAsString());
        }
        
        // if($web){
        //     return $rendered;
        // }else if($api){
        //     return response()->json([
        //         'status'    => 'error',
        //         'message'   => $message,
        //         'data'      => null,
        //         'errors'    => $errors,
        //     ], $status_code);
        // }

        // if($request->ajax()){
        //     return response()->json([
        //         'status'    => 'error',
        //         'message'   => $message,
        //         'data'      => null,
        //         'errors'    => $errors,
        //     ], $status_code);
        // }else{
        //     return $rendered;
        // }

        // ! kalo misal mau pake handler biasa, bawaan laravel
        // return $rendered;


        // ! kalo mau pake custom exception, bertipe json -> tanpa perlu utak atik di controller
        return response()->json([
            'status'    => 'error',
            'message'   => $message,
            'data'      => null,
            'errors'    => $errors,
        ], $status_code);
    }

    // * Fungsi catch exp autentikasi
    protected function unauthenticated($request, AuthenticationException $exception){
        return response()->json([
            'status' => 'error',
            'message' => 'Authentication',
            'data' => null
        ], 401);
    }

    protected function base_path(){
        return $path = 'routes/web.php';
    }
}
