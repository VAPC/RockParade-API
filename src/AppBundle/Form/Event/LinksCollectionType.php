<?php

namespace AppBundle\Form\Event;

use AppBundle\Form\AbstractFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Vehsamrak
 */
class LinksCollectionType extends AbstractFormType
{

    /** {@inheritDoc} */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('links', TextType::class);
    }

    protected function getDataClass(): string
    {
        return LinksCollectionDTO::class;
    }
}
