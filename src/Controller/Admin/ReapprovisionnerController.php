<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Reaprosionner;
use App\Form\ReaprosionnerType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/reapprovisionner')]
final class ReapprovisionnerController extends AbstractController
{

    #[Route('/', name: 'app_reapprovisionner', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        return $this->render("reapprovisionner/index.html.twig", [
            'products' => $productRepository->findAll(),
        ]);
    }

    
}
