<?php


namespace BristolSU\Auth\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Linkeys\UrlSigner\Exceptions\LinkNotFoundException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        EmailNotVerified::class,
        PasswordUnconfirmed::class
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
     * @param Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if (!$request->expectsJson()) {
            if ($exception instanceof EmailNotVerified) {
                return redirect()->route('verify.notice');
            }
            if($exception instanceof LinkNotFoundException) {
                $request->session()->flash('messages', 'This link has expired.');
                return redirect()->route('verify.notice');
            }
            if ($exception instanceof PasswordUnconfirmed) {
                return redirect()->route('password.confirmation.notice');
            }
        } else {
            if ($exception instanceof EmailNotVerified) {
                return response()->json('You must verify your email address.', 403);
            }
            if ($exception instanceof PasswordUnconfirmed) {
                return response()->json('Password confirmation required.', 423);
            }
            if ($exception instanceof LinkNotFoundException) {
                return response()->json('This link has expired.', 403);
            }
        }
        return parent::render($request, $exception);
    }
}
