<?php

// src/Controller/ProductController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// ...
use App\Entity\Images;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImagesController extends AbstractController
{
    #[Route('/images', name: 'create_product')]
    public function createImage(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $image = new Images();
        $image->setName('Jungle-pic');
        $image->setTag('nature');
        $image->setProvider('unsplash');
        $image->setCreatedOn( new \DateTime());

        // tell Doctrine you want to (eventually) save the image (no queries yet)
        $entityManager->persist($image);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$image->getId());
    }
}
