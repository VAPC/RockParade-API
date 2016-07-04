<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Vehsamrak
 */
class CreateBandMemberDTO extends UpdateBandMemberDTO
{
    /**
     * @var string
     * @Assert\NotBlank(message="Parameter 'login' is mandatory")
     */
    public $login;
}
