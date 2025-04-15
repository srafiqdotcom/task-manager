<?php
declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

class ShutdownHandler
{
    private Request $request;
    private HttpErrorHandler $errorHandler;
    private bool $displayErrorDetails;

    public function __construct(
        Request $request,
        HttpErrorHandler $errorHandler,
        bool $displayErrorDetails
    ) {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    public function __invoke()
    {
        $error = error_get_last();

        // Only act on fatal errors
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                $message = sprintf(
                    "FATAL ERROR: %s on line %d in file %s.",
                    $errorMessage,
                    $errorLine,
                    $errorFile
                );
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);
            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                false,
                false
            );

            // Clean output buffer before sending response
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            if (!headers_sent()) {
                $responseEmitter = new ResponseEmitter();
                $responseEmitter->emit($response);
            }
        }
    }
}
