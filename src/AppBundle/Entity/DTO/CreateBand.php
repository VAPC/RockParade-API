<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Vehsamrak
 */
class CreateBand
{
    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: name.")
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank(message="Parameter is mandatory: description.")
     */
    public $description;

    /**
     * @var string[]
     * @Assert\NotBlank(message="Parameter is mandatory: members.")
     */
    public $members;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!is_array($this->members)) {
            $context->buildViolation('Members must be an array.')
                    ->atPath('members')
                    ->addViolation();
        }
    }
}
