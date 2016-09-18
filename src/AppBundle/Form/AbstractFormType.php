<?php

namespace AppBundle\Form;

use AppBundle\Exception\MethodNotImplemented;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Vehsamrak
 */
abstract class AbstractFormType extends AbstractType
{

    abstract public function getEntityClassName(): string;

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
            $builder->add($this->underscore($fieldName));
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

    private function underscore(string $string): string
    {
        $string = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string);

        return strtolower($string);
    }
}
