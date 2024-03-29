<?php


namespace BristolSU\Auth\Exceptions;

use BristolSU\Auth\Settings\Access\DefaultHome;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\Authentication\Exception\IsAuthenticatedException;
use BristolSU\Support\Authentication\Exception\PasswordUnconfirmed;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Linkeys\UrlSigner\Exceptions\LinkNotFoundException;
use Throwable;

class Handler implements ExceptionHandler
{

    /**
     * @var ExceptionHandler
     */
    private ExceptionHandler $handler;

    public function __construct(ExceptionHandler $handler)
    {
        $this->handler = $handler;
    }

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
                redirect()->setIntendedUrl($request->path());
                return redirect()->route('verify.notice');
            }
            if($exception instanceof LinkNotFoundException) {
                $request->session()->flash('messages', 'This link has expired.');
                return redirect()->route('verify.notice');
            }
            if ($exception instanceof PasswordUnconfirmed) {
                redirect()->setIntendedUrl($request->path());
                return redirect()->route('password.confirmation.notice');
            }
            if($exception instanceof IsAuthenticatedException) {
                $auth = app(Authentication::class);
                if(!$auth->hasUser()) {
                    return redirect()->route('login');
                }
                return redirect()->route(DefaultHome::getValueAsRouteName($auth->getUser()->id()));
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
            if($exception instanceof IsAuthenticatedException) {
                return response()->json('You must not be logged in to access this page.');
            }
        }
        return $this->handler->render($request, $exception);
    }

    public function report(Throwable $e)
    {
        if($e instanceof EmailNotVerified || $e instanceof PasswordUnconfirmed || $e instanceof IsAuthenticatedException) {
            return;
        }

        $this->handler->report($e);
    }

    public function shouldReport(Throwable $e)
    {
        return $this->handler->shouldReport($e);

    }

    public function renderForConsole($output, Throwable $e)
    {
        $this->handler->renderForConsole($output, $e);
    }
}
