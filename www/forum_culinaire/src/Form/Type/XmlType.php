<?php

namespace forum_culinaire\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class XmlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        
        $builder->add('choixCat', ChoiceType::class, array( 
            'choices'  => array(
                                'Apéritifs'=> 1,
                                'Entrées' => 2,
                                'Plat'=> 3,
                                'dessert' => 4)
            ));
                      
    }

    public function getName()
    {
        return 'XML';
    }
}


