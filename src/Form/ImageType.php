<?php 
// src/Form/IMageType.php
namespace App\Form;

use App\Entity\Images;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ImageType extends AbstractType
{
    
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name', TextType::class)
      ->add('tag', TextType::class)
      ->add('provider', TextType::class)
      ->add('url', TextType::class)
      ->add('imageFile', FileType::class ,[
          'label' => 'Image',
          'mapped' => false,
          'required' => false,
          'constraints' => [
            new File([
                'maxSize' => '2048k',
                'mimeTypes' => [
                  'image/jpg',
                  'image/jpeg',
                  'image/png',
                ],
                'mimeTypesMessage' => 'Please upload a valid image file',
            ])
          ],
        ])
      ->add('created_on', DateType::class)
    ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => Images::class,
    ));
  }
}