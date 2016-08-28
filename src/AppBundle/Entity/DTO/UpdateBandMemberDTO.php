<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class UpdateBandMemberDTO
{
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
