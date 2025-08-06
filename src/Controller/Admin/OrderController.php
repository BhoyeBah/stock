<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Repository\ClientRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/order')]
final class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(ProductRepository $productRepository, ClientRepository $clientRepository, Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $products = $productRepository->findAll();
        $clients = $clientRepository->findAll();

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order, [
            'clients' => $clients
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();

            $productIds = $data['products_id'] ?? [];
            $prixUnits = $data['prixUnit'] ?? [];
            $quantites = $data['quantite'] ?? [];
            $montantPaye = intval($data['montantPaye'] ?? 0);

            $montantTotal = 0;

            foreach ($productIds as $index => $productId) {
                $product = $productRepository->find($productId);
                if (!$product) {
                    continue;
                }

                $prixUnit = intval($prixUnits[$index] ?? 0);
                $quantite = intval($quantites[$index] ?? 0);
                $prixTotal = $prixUnit * $quantite;
                $montantTotal += $prixTotal;

                $orderDetail = new OrderDetail();
                $orderDetail->setProduct($product);
                $orderDetail->setOrders($order);
                $orderDetail->setPrixUnit($prixUnit);
                $orderDetail->setQuantite($quantite);
                $orderDetail->setPrixTotal($prixTotal);

                $order->addOrderDetail($orderDetail);
                $entityManager->persist($orderDetail);
            }
            $numero = $orderRepository->generateOrderNumber();

            // Calculs globaux
            $order->setNumero($numero);
            $order->setUser($this->getUser());
            $order->setMontantTotal($montantTotal);
            $order->setMontantPaye($montantPaye);
            $order->setMontantRestant($montantTotal - $montantPaye);
            $order->setSolde($montantPaye - $montantTotal);
            $order->setCreatedAt(new \DateTime());

            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash('success', 'Facture enregistrée avec succès.');

            return $this->redirectToRoute('app_order');
        }

        return $this->render('order/new.html.twig', [
            'products' => $products,
            'clients' => $clients,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order)
    {
        
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    
    #[Route('/imprimer/{order}', name: 'app_order_print', methods: ['GET'])]
    public function print(Order $order)
    {
        
        return $this->render('order/show_print.html.twig', [
            'order' => $order,
        ]);
    }
}
