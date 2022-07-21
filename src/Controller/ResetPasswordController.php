<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager=$entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if($this->getUser()){
            return  $this->redirectToRoute('home');
        }

        if($request->get('email')){

            //etape 1 enregistrer en base la demande reset password
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('email') ]);
            if($user){
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt( new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();
            //etape 2 envoyer un email au user avec un lien pour mettre a jour son password

                $url= $this->generateUrl('update_password',[
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ".$user->getLastname()."<br>Vous avez demandé à réinitailiser votre mot de passe sur MaBoutique.<br><br>";
                $content .="Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'>mettre a jour votre mot de passe.</a>";

                $mail = new Mail();
                $mail->send($user->getEmail(),$user->getFirstname().' '.$user->getLastname(),'Réinitialiser votre mot de passe sur MaBoutique',$content);
                $this->addFlash('notice','Vous allez recevoir un mail dans quelques minutes avec la procédure pour réinitialiser votre mot de passe.');

            }
            else{
                $this->addFlash('notice','Cette adresse email est inconnue.');

            }
        }
        return $this->render('reset_password/index.html.twig');
    }


    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'update_password')]
    public function update($token, Request $request,UserPasswordHasherInterface $hasher): Response{

        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneBy(['token' => $token]);

        if(!$reset_password){
            return $this->redirectToRoute('reset_password');
        }

        //verifier si le createdAt du token = now - 3h
        $now = new \DateTime();
        if( $now > $reset_password->getCreatedAt()->modify('+3 hour')){
            $this->addFlash('notice','Votre lien de renouvellement a expiré bien vouloir le renouvelé.');
            return $this->redirectToRoute('reset_password');
        }

        //rendre une vue avec le mot de passe et confirmer le mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData();
            $user= $reset_password->getUser();

            //encodage des password
            $password = $hasher->hashPassword($user,$new_pwd);

            $user->setPassword($password);

            //flush
            $this->entityManager->flush();

            //redirection vers la page connexion

            $this->addFlash('notice','Votre mot de passe a bien été mis-à-jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
