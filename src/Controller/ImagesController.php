<?php

// src/Controller/ProductController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Images;
use App\Form\ImageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Component\Routing\Annotation\Route;

class ImagesController extends AbstractController
{
  #[Route('/new', name: 'Image_new')]
  public function register(Request $request, ManagerRegistry $doctrine)
  {
    $image = new Images();
    $form = $this->createForm(ImageType::class, $image);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $doctrine->getManager();
      $entityManager->persist($image);
      $entityManager->flush();
      return $this->redirectToRoute('Image_listing');
    }

    return $this->render(
      'images/new.html.twig',
     ['form' => $form->createView()]
    );
  }

  #[Route('/listing', name: 'Image_listing')]
  public function Listing(ManagerRegistry $doctrine): Response
  {
      $entityManager = $doctrine->getManager();
      $query = $entityManager->createQuery("SELECT p FROM  App\Entity\Images p");
      $result = $query->getArrayResult();

      return $this->render(
        'images/listing.html.twig',
        ['data' => $result]
      );
  }
}
