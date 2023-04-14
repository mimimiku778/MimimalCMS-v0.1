<?php

declare(strict_types=1);

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\Reception;
use Shadow\Kernel\Session;
use Shadow\Kernel\ResponseInterface;
use Shadow\Kernel\ResponseHandler;
use Shadow\Kernel\RouteClasses\RouteDTO;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ReceptionInitializer implements ReceptionInitializerInterface
{
    private RouteDTO $routeDto;
    private ?ResponseInterface $routeFails = null;

    public function __construct(RouteDTO $routeDto)
    {
        $this->routeDto = $routeDto;
        $this->routeFails = $routeDto->getFailsResponse();

        Reception::$controllerClassName = $this->routeDto->controllerClassName;
        Reception::$methodName =          $this->routeDto->methodName;

        $this->getDomainAndHttpHost();
        Reception::$requestMethod =       $this->routeDto->requestMethod;
        Reception::$isJson =              $this->routeDto->isJson;

        Reception::$flashSession =        $this->getFlashSession();
        Reception::$inputData =           $this->parseRequestBody($this->routeDto->paramArray);
    }

    public static function getDomainAndHttpHost(): string
    {
        if (isset(Reception::$domain)) {
            return Reception::$domain;
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        Reception::$domain = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? '');

        return Reception::$domain;
    }

    /**
     * Get the flash session data if it exists and unset it from the session.
     *
     * @return array The flash session data
     */
    private function getFlashSession(): array
    {
        if (isset($_SESSION[FLASH_SESSION_KEY_NAME])) {
            $session = $_SESSION[FLASH_SESSION_KEY_NAME];
            unset($_SESSION[FLASH_SESSION_KEY_NAME]);
        } else {
            $session = [];
        }

        return $session;
    }

    /**
     * Parses the request body and returns the input data.
     *
     * @return array|null The input data passed with the incoming request, or null.
     */
    private function parseRequestBody(array $paramArray): array
    {
        if (Reception::$requestMethod === 'GET') {
            return array_merge($_GET, $paramArray);
        }

        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $requestBody = file_get_contents('php://input');
            if (!is_string($requestBody)) {
                return [];
            }

            $jsonArray = json_decode($requestBody, true);
            if (!is_array($jsonArray)) {
                return [];
            }

            return array_merge($_GET, $jsonArray, $paramArray, $_FILES);
        }

        return array_merge($_GET, $_POST, $paramArray, $_FILES);
    }

    /**
     * Validate the incoming request using the built-in validators and the route callback validator, if available.
     * Store the validated input data in the static Reception::$inputData property.
     * 
     * @param array $inputArray
     * 
     * @return array Validated array
     * 
     * @throws InvalidArgumentException
     * @throws \ValidationException
     * @throws \NotFoundException
     */
    public function callRequestValidator()
    {
        $builtinValidators = $this->routeDto->getValidater();
        $routeCallback = $this->routeDto->getRouteCallback();

        if ($builtinValidators === false) {
            if ($routeCallback === false) {
                Reception::$inputData = [];
                return;
            }

            $validatedArray = $this->validateCallbackRoute($routeCallback);
        } else {
            $validatedArray = $this->validateUsingBuiltinValidators($builtinValidators);
            if ($routeCallback !== false) {
                $callbackValidatedArray = $this->validateCallbackRoute($routeCallback);
                if (!empty($callbackValidatedArray)) {
                    $validatedArray = array_merge($validatedArray, $callbackValidatedArray);
                }
            }
        }

        Reception::$inputData = $validatedArray;
    }

    /**
     * Validate the incoming request using the built-in validators and return the validated input data.
     */
    private function validateUsingBuiltinValidators($validators)
    {
        $validatedArray = $this->callBuiltinValidator($validators);
        $routeCallback = $this->routeDto->getRouteCallback();
        if ($routeCallback === false) {
            return $validatedArray;
        }

        $callbackValidatedArray = $this->validateCallbackRoute($routeCallback);
        if (empty($callbackValidatedArray)) {
            return $validatedArray;
        }

        return array_merge($validatedArray, $callbackValidatedArray);
    }

    /**
     * Validate the incoming request using the given route callback and return the validated input data.
     */
    private function validateCallbackRoute($routeCallback)
    {
        $callbackValidatedArray = $this->callbackRouteValidator($routeCallback);
        if (empty($callbackValidatedArray)) {
            return [];
        }
        return $callbackValidatedArray;
    }

    /**
     * Validate the incoming request using the given route callback and return the validated input data.
     */
    private function callbackRouteValidator(\Closure $routeCallback): array
    {
        [$closureArgs, $validatedArray] = $this->getClosureArgs($routeCallback);

        try {
            $response = new ResponseHandler($routeCallback(...$closureArgs));
            $result = $response->handleResponse();
        } catch (\Throwable $e) {
            $this->errorResponse([
                ['key' => 'match', 'code' => $e->getCode(), 'message' => $e->getMessage()]
            ]);
        }

        if ($result === true) {
            return Reception::$inputData;
        } elseif ($result === false) {
            $this->errorResponse([['key' => 'match']]);
        } elseif (is_array($result)) {
            return $result;
        }

        return $validatedArray;
    }

    /**
     * Get the arguments for the given closure function and return an array of both the closure arguments and validated input data.
     */
    private function getClosureArgs(\Closure $function): array
    {
        $reflection = new \ReflectionFunction($function);
        $parameters = $reflection->getParameters();

        $closureArgs = [];
        $builtinValidatedArray = [];

        foreach ($parameters as $param) {
            $paramType = $param->getType();

            if ($paramType === null || $paramType->isBuiltin()) {
                $paramName = $param->getName();
                $closureArgs[] = Reception::$inputData[$paramName] ?? null;
                $builtinValidatedArray[$paramName] = Reception::$inputData[$paramName] ?? null;
                continue;
            }

            $paramClassName = $paramType->getName();
            if (!class_exists($paramClassName)) {
                throw new \InvalidArgumentException(
                    'Invalid class name: "' . $paramType . '" not found: $' . $param->getName()
                );
            }

            $closureArgs[] = new $paramClassName();
        }

        return [$closureArgs, $builtinValidatedArray];
    }

    private function callBuiltinValidator(array $validators): array
    {
        $validatedArray = [];
        $errors = [];

        foreach ($validators as $key => $validator) {
            $data = Reception::$inputData;
            $currentLevel = &$validatedArray;

            foreach (explode('.', $key) as $property) {
                $data = &$data[$property] ?? null;
                $currentLevel[$property] = null;
                $currentLevel = &$currentLevel[$property];
            }

            if ($data === null) {
                $errors[] = [
                    'key' => $key,
                    'code' => 0,
                    'message' => 'no value'
                ];
            }

            try {
                $validatedValue = $validator($data);
            } catch (\ValidationException $e) {
                $errors[] = [
                    'key' => $key,
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
                $validatedValue = false;
            }

            $currentLevel = $validatedValue;
        }

        if (!empty($errors)) {
            $this->errorResponse($errors);
        }

        return $validatedArray;
    }

    /**
     * Generate error response.
     *
     * @param array $errorArray List of error details, each containing 'key', 'code', and 'message'.
     * @throws \NotFoundException
     * @throws \InvalidInputException
     */
    private function errorResponse(array $errorArray)
    {
        if ($this->routeFails !== null) {
            foreach ($errorArray as $error) {
                Session::addError($error['key'], $error['code'], $error['message']);
            }

            $this->routeFails->send();
        }

        $message = $errorArray[0]['message'] ?? 'Request validation failed.';
        $code = $errorArray[0]['code'] ?? 0;

        if (Reception::$requestMethod === 'GET') {
            throw new \NotFoundException($message, $code);
        } else {
            throw new \InvalidInputException($message, $code);
        }
    }
}
