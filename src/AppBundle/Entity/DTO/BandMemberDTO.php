<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class BandMemberDTO
{
    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'login' is mandatory")
     */
    public $login;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'short_description' is mandatory")
     */
    private $shortDescription;
    
    /**
     * @var string
     */
    public $description;

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }
}
