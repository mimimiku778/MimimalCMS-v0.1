<?php

namespace Shadow\Kernel\Dispatcher;

use Shadow\Kernel\RouteClasses\RouteDTO;

interface MiddlewareInvokerInterface
{
    public function invoke(RouteDTO $routeDto);
}