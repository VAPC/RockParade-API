<?php

namespace AppBundle\Service\Vkontakte;

/**
 * @author Vehsamrak
 */
class AccessToken
{

    /** @var int */
    public $userVkId;

    /** @var string */
    public $userEmail;

    /** @var string */
    private $tokenHash;

    public function __construct(int $userVkId, string $tokenHash, string $userEmail = null)
    {
        $this->userVkId = $userVkId;
        $this->tokenHash = $tokenHash;
        $this->userEmail = $userEmail;
    }

    public function __toString(): string
    {
        return $this->tokenHash;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }
}
