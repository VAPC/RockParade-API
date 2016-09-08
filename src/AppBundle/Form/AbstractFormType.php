<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Vehsamrak
 */
abstract class AbstractFormType extends AbstractType
{

    /** {@inheritDoc} */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => $this->getDataClass(),
                'allow_extra_fields' => true,
            ]
        );
    }

    abstract protected function getDataClass(): string;
}
