<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager=$entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_success')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['stripeSessionId'=> $stripeSessionId]);
            if(!$order || $order->getUser() != $this->getUser()){
                return $this->redirectToRoute('home');//securite si la commande n'existe pas ou si ce n'est pas le bon user
            }

            if($order->getState() == 0){
                //Vider la session card
                   $cart->remove();

                //Modifier le status isPaid
                $order->setState(1);

                $this->entityManager->flush();

                //Envoyer un mail pour confirmer la commande

                $content = "Bonjour à toi ".$order->getUser()->getFirstname()."Merci pour votre commande<br>Nous sommes ravis de te compter parmis nous.";

                $mail = new Mail();
                $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande sur MaBoutique est bien validéé.',$content);
            }


          //Afficher quelques infos de la commande a l'utilisateur
        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
