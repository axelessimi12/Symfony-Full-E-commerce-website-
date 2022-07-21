<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelContollerController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager=$entityManager;
    }

    #[Route('/commande/erreur/{stripeSessionId}', name: 'order_cancel')]
    public function index($stripeSessionId): Response
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId'=> $stripeSessionId]);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');//securite si la commande n'existe pas ou si ce n'est pas le bon user
        }

        //Envoyer un email à l'utilisteur pour lui indiquer l'echec du paiement

        return $this->render('order_cancel/index.html.twig',[
            'order' => $order
        ]);
    }
}
