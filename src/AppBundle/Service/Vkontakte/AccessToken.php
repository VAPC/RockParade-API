<?php

namespace AppBundle\Service\Vkontakte;

/**
 * @author Vehsamrak
 */
class AccessToken
{

    /** @var int */
    public $userVkontakteId;

    /** @var string */
    public $userEmail;

    /** @var string */
    private $vkontakteTokenHash;

    public function __construct(int $userVkontakteId, string $vkontakteTokenHash, string $userEmail = '')
    {
        $this->userVkontakteId = $userVkontakteId;
        $this->vkontakteTokenHash = $vkontakteTokenHash;
        $this->userEmail = $userEmail;
    }

    public function getVkontakteTokenHash(): string
    {
        return $this->vkontakteTokenHash;
    }

    public function getUserVkontakteId(): int
    {
        return $this->userVkontakteId;
    }
}
