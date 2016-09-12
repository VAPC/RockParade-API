<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

    /** {@inheritDoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dataClassVariables = array_keys(get_class_vars($this->getDataClass()));

        foreach ($dataClassVariables as $fieldName) {
            $builder->add($fieldName);
        }
    }

    /**
     * Form data object class name
     * @return string class name
     */
    protected function getDataClass(): string
    {
        return static::class;
    }
}
