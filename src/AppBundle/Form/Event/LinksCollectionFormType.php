<?php

namespace AppBundle\Form\Event;

use AppBundle\Form\AbstractFormType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Vehsamrak
 */
class LinksCollectionFormType extends AbstractFormType
{

    /** @var array */
    public $links;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (empty($this->links)) {
            $context->buildViolation('Parameter is mandatory: links.')->addViolation();
        }

        if (!is_array($this->links)) {
            $context->buildViolation('Links must be an array.')->addViolation();
        } else {
            foreach ($this->links as $linkKey => $link) {
                $url = $link['url'] ?? null;

                if (!$url) {
                    $context->buildViolation(sprintf('Parameter is mandatory: links[%s][url].', $linkKey))
                            ->addViolation();
                }
            }
        }
    }
}
