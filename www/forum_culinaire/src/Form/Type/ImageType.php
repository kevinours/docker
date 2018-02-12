<?php
// src/OC/PlatformBundle/Form/ImageType.php

namespace forum_culinaire\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('file', FileType::class, array('label' => 'Choisissez votre nouvelle image de profil'))   
    ;
  }
}