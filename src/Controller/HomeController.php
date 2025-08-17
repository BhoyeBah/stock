<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\OrderRepository;
use App\Repository\PaiementRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        ClientRepository $clientRepository,
        PaiementRepository $paiementRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $totalOrders = $orderRepository->count();
        $nbreOrderNonPaye = $orderRepository->countUnpaid();
        $pourcentageNonPaye = $totalOrders > 0 ? round(($nbreOrderNonPaye / $totalOrders) * 100) : 0;

        $paiementsParMois = $paiementRepository->getMontantPayeParMois();
        $caLabels = array_keys($paiementsParMois);
        $caMontants = array_values($paiementsParMois);

        return $this->render('home/index.html.twig', [
            'order' => $orderRepository->count(),
            'product' => $productRepository->count(),
            'client' => $clientRepository->count(),
            'montantTotal' => $orderRepository->getMontantTotal(),
            'montantPaye' => $paiementRepository->getMontantPaye(),
            'montantNonPaye' => $orderRepository->getMontantNonPaye(),
            'nbreOrderPaye' => $orderRepository->countPaid(),
            'nbreOrderNonPaye' => $nbreOrderNonPaye,
            'pourcentageOrderNonPaye' => $pourcentageNonPaye,
            'caLabels' => $caLabels,
            'caMontants' => $caMontants,
        ]);
    }
}
