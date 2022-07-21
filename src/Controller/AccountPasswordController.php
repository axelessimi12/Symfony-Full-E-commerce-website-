<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    #[Route('/compte/modifier-mon-mot-de-passe', name: 'account_password')]
    public function index(Request $request, UserPasswordHasherInterface $hasher, \Doctrine\Persistence\ManagerRegistry $doctrine): Response
    {
        $notification = null;
        $success = false;
        $entityManager = $doctrine->getManager();
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user,);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()) {
            $old_pwd= $form->get('old_password')->getData();


            if($hasher->isPasswordValid($user,$old_pwd)){
                $new_pwd= $form->get('new_password')->getData();
                $password = $hasher->hashPassword($user,$new_pwd);

                $user->setPassword($password);
                $entityManager->flush();
                $notification = "Votre mot de passe a bien été mis à jour";
                $success = true;
            }
            else{
                $notification = "Votre mot de passe n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig',[
            'form'=> $form->createView(),
            'notification' => $notification,
            'success' => $success,
        ]);
    }
}
