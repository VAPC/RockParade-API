<?php

namespace AppBundle\Enum;

use MyCLabs\Enum\Enum;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vehsamrak
 */
class ApiOperation extends Enum
{

    const CREATE = Request::METHOD_POST;
    const VIEW = Request::METHOD_GET;
    const UPDATE = Request::METHOD_PUT;
    const DELETE = Request::METHOD_DELETE;
}
