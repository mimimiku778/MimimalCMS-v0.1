<?php

declare(strict_types=1);

namespace Shadow\Kernel\RouteClasses;

use Shadow\Kernel\ResponseInterface;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class RouteDTO
{
    /**
     * `['routePath']`  
     */
    public array $routePathArray = [];

    /**
     * `['routePathArrayKey' => ['requestMethod' => ['parametarName' => $Closure]]]`  
     */
    public array $routeValidatorArray = [];

    /**
     * `['routePathArrayKey' => ['requestMethod' => $Closure]]`  
     */
    public array $routeCallbackArray = [];

    /**
     * `['routePathArrayKey' => ['requestMethod' => ['controllerClassName ', 'methodName']]]`  
     */
    public array $routeExplicitControllerArray = [];

    public array $routeFailsArray = [];

    public array $routeMiddlewareArray = [];

    public array $kernelMiddlewareArray = [];

    /**
     * `['currentPath']`  
     */
    public array $parsedPathArray;

    /**
     * `['currentURIParameta']`  
     */
    public array $paramArray;

    /**
     * `['currentAllowedRequestMethod']`  
     */
    public array|false $routeRequestMethodArray;

    /**
     * Current key of routePathArray
     */
    private int|string $routeArrayKey;

    /**
     * `['currentControllerClassName']`  
     */
    public string $controllerClassName;

    /**
     * `['currentControllerMethodName']`  
     */
    public string $methodName;

    /**
     * `['currentRequestMethod']`  
     */
    public string $requestMethod;
    public bool $isJson;

    public function __construct()
    {
        $this->isJson = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
    }

    public function setRouteArrayKey(int|string $key)
    {
        $this->routeArrayKey = $key;
    }

    /**
     * @return array|false `['controllerClassName ', 'methodName']`
     */
    public function getExplicitControllerArray(): array|false
    {
        return $this->routeExplicitControllerArray[$this->routeArrayKey][$this->requestMethod] ?? false;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getFailsResponse(): ResponseInterface|null
    {
        return $this->routeFailsArray[$this->routeArrayKey][$this->requestMethod] ?? null;
    }

    /**
     * @return array|false `['parametarName' => $Closure]`
     */
    public function getValidater(): array|false
    {
        return $this->routeValidatorArray[$this->routeArrayKey][$this->requestMethod] ?? false;
    }

    /**
     * @return \Closure|false Callback function passed in the routing definition.
     */
    public function getRouteCallback(): \Closure|false
    {
        return $this->routeCallbackArray[$this->routeArrayKey][$this->requestMethod] ?? false;
    }

    /**
     * @return array|false `['middlewareName']`
     */
    public function getMiddlewares(): array|false
    {
        $kernelMiddlewareArray = $this->kernelMiddlewareArray ?? [];
        $routeMiddlewareArray = $this->routeMiddlewareArray[$this->routeArrayKey][$this->requestMethod] ?? [];

        if($kernelMiddlewareArray === [] && $routeMiddlewareArray === []) {
            return false;
        }

        return array_merge($kernelMiddlewareArray, $routeMiddlewareArray);
    }
}
