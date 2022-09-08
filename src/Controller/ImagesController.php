<?php

// src/Controller/ProductController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Images;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Component\Routing\Annotation\Route;

class ImagesController extends AbstractController
{
    #[Route('/add_images', name: 'add_new_image')]
    public function createImage(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $image = new Images();
        $image->setName('Jungle-pic');
        $image->setTag('nature');
        $image->setProvider('unsplash');
        $image->setCreatedOn( new \DateTime());

        $form = $this->createFormBuilder($image)
        ->add('name', TextType::class)
        ->add('tag', DateType::class)
        ->add('provider', DateType::class)
        ->add('created_on', DateType::class)
        ->add('save', SubmitType::class, ['label' => 'Add image'])
        ->getForm();

        $entityManager->persist($image);
        $entityManager->flush();

        

        return new Response('Saved new product with id '.$image->getId());
    }

    #[Route('/listing', name: 'Image_listing')]
    public function Listing(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $query = $entityManager->createQuery("SELECT p FROM  App\Entity\Images p");
        $result = $query->getArrayResult();

        echo "<pre>";
          print_r($result);
        echo "</pre>";
        die;
        // return new Response('Saved new product with id '.$image->getId());
    }
}
