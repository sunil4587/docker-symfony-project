<?php

// src/Controller/ProductController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Images;
use App\Form\ImageType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Component\Routing\Annotation\Route;

class ImagesController extends AbstractController
{
  public function __construct(ManagerRegistry $doctrine)
  {
    $this->em = $doctrine->getManager();
    $this->doc = $doctrine;
  }

  #[Route('/new', name: 'Image_new')]
  public function addNew(Request $request)
  {
    $image = new Images();
    $form = $this->createForm(ImageType::class, $image);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
      $imageFile = $form->get('imageFile')->getData();
      $this->em->persist($image);
      $this->em->flush();
      $imageId = $image->getId() ? $image->getId() : false;

      if ($imageFile && $imageId) {
        $newFilename = $imageId.'.'.$imageFile->guessExtension();
        try {
          $imageFile->move(
            $this->getParameter('images_dir'),
            $newFilename
          );
          $imageData = $this->em->getRepository(Images::class)->find($imageId);
          if (!$imageData) {
            throw $this->createNotFoundException(
                'No product found for id '.$imageId
            );
          }
          $image->setImageFile($newFilename);
          $this->em->flush();

        } catch (FileException $e) {
          echo $e->getMessage();
          die;
        }
      }
      return $this->redirectToRoute('Image_listing');
    }

    return $this->render(
      'images/new.html.twig',
     ['form' => $form->createView()]
    );
  }

  #[Route('/edit/{id}', name: 'Image_edit')]
  public function edit(Request $request, $id)
  {
    $image = $this->doc->getRepository(IMages::class)->find($id);
    $form = $this->createForm(ImageType::class, $image);
    $form->handleRequest($request);
    
    if($form->isSubmitted() && $form->isValid()) {
      $imageFile = $form->get('imageFile')->getData();
      $this->em->persist($image);
      $this->em->flush();
      $imageId = $image->getId() ? $image->getId() : false;

      if ($imageFile && $imageId) {
        $newFilename = $imageId.'.'.$imageFile->guessExtension();
        try {
          $imageFile->move(
            $this->getParameter('images_dir'),
            $newFilename
          );
          $imageData = $this->em->getRepository(Images::class)->find($imageId);
          if (!$imageData) {
            throw $this->createNotFoundException(
                'No image found for id '.$imageId
            );
          }
          $image->setImageFile($newFilename);
          $this->em->flush();

        } catch (FileException $e) {
          echo $e->getMessage();
          die;
        }
      }
      return $this->redirectToRoute('Image_listing');
    }

    return $this->render(
      'images/edit.html.twig',
     ['form' => $form->createView()]
    );
  }


  #[Route('/listing', name: 'Image_listing')]
  public function Listing(): Response
  {
    if(!empty($_GET['search'])){
      $search = $_GET['search'];
      $result =  $this->em->getRepository(Images::class)->createQueryBuilder('o')
      ->where('o.tag = :search')
      ->orWhere('o.provider = :search')
      ->setParameter('search', $search)
      ->getQuery()
      ->getArrayResult();
    }else{
      $query =  $this->em->createQuery("SELECT i FROM App\Entity\Images i");
      $result = $query->getArrayResult();
    }

    return $this->render(
      'images/listing.html.twig',
      ['data' => $result]
    );
  }

  #[Route('/gallary', name: 'Image_gallary')]
  public function gallary(): Response
  {
    $search = '';
    if(!empty($_GET['search'])){
      $search = $_GET['search'];
      $result =  $this->em->getRepository(Images::class)->createQueryBuilder('o')
      ->where('o.tag = :search')
      ->orWhere('o.provider = :search')
      ->setParameter('search', $search)
      ->getQuery()
      ->getArrayResult();
    }else{
      $query =  $this->em->createQuery("SELECT i FROM App\Entity\Images i");
      $result = $query->getArrayResult();
    }

    return $this->render(
      'images/gallary.html.twig',
      [
        'data' => $result,
        'searched' => $search,
      ]
    );
  }

  #[Route('/getData', name: 'Image_data')]
  public function getData(): Response
  {
    if(!empty($_GET['search'])){
      $search = $_GET['search'];
      $result =  $this->em->getRepository(Images::class)->createQueryBuilder('o')
      ->where('o.tag = :search')
      ->orWhere('o.provider = :search')
      ->setParameter('search', $search)
      ->getQuery()
      ->getArrayResult();
    }else{
      $query =  $this->em->createQuery("SELECT i FROM App\Entity\Images i");
      $result = $query->getArrayResult();
    }

    $response = new Response(json_encode($result));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  #[Route('/delete/{id}', name: 'Image_delete')]
  public function delete($id): Response
  {
    $repository = $this->doc->getRepository(Images::class);
    $image = $repository->find($id);
    $this->em->remove($image);
    $this->em->flush();

    return $this->redirectToRoute('Image_listing');
  }
}
