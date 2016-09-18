<?php

namespace AppBundle\Exception;

use AppBundle\Enum\ApiOperation;

/**
 * @author Vehsamrak
 */
class UnsupportedApiOperation extends HttpRuntimeException
{

    /**
     * @param ApiOperation $apiOperation
     */
    public function __construct(ApiOperation $apiOperation)
    {
        parent::__construct(sprintf('Api operation "%s" is not supported.', $apiOperation->getValue()));
    }

}
