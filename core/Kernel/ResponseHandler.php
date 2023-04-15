<?php

declare(strict_types=1);

namespace Shadow\Kernel;

use Shadow\Exceptions\NotFoundException;
use Shadow\Exceptions\BadRequestException;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ResponseHandler implements ResponseHandlerInterface
{
    public function handleResponse(mixed $response): mixed
    {
        if ($response instanceof ViewInterface) {
            $response->render();
            return true;
        }

        if ($response instanceof ResponseInterface) {
            $response->send();
            return true;
        }

        if ($response instanceof \Closure) {
            ($response)();
            return true;
        }

        if ($response === false) {
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
                throw new NotFoundException('no response');
            }

            throw new BadRequestException('no response');
        }

        if (is_string($response)) {
            echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
            return true;
        }

        return $response;
    }
}
