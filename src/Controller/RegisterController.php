<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
    public function index(Request $request,\Doctrine\Persistence\ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher ): Response
    {
        $notification = "";
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $user = new User();
            $user = $form->getData();

            $search_email = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if(!$search_email){
                // tell Doctrine you want to (eventually) save the Product (no queries yet)
                $password = $user->getPassword();
                $hashedPassword = $hasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);


                $entityManager->persist($user);

                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                $content = "Bonjour à toi ".$user->getFirstname()." bienvenue sur la meilleure boutique de vente d'articles du Cameroun.<br>Nous sommes ravis de te compter parmis nous.";

                $mail = new Mail();
                $mail->send($user->getEmail(),$user->getFirstname(),'Bienvenue sur MaBoutique',$content);

                $notification = "Votre inscription s'est correctement déroulée, vous pouvez vous connecter à present.";
            }
            else{
                $notification = "L'email renseigné existe déja.";
            }

        }

        return $this->render('register/index.html.twig',[
            'form'=>$form->createView(),
            'notification' => $notification
        ]);
    }
}
