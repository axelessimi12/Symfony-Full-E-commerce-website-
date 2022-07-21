<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager= $entityManager;
    }

    #[Route('/compte/adresses', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'account_address_add')]
    public function add(Cart $cart, Request $request): Response
    {
        /** @var $user \App\Entity\User */
        $user = $this->getUser();

        $adresse = new Address();
        $form = $this->createForm(AddressType::class, $adresse);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $adresse->setUser( $user);
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();
            if($cart->get()){
                return $this->redirectToRoute('order');
            }
            else{
                return $this->redirectToRoute('account_address');
            }
        }
        return $this->render('account/address_formulaire.html.twig',[
            'form' => $form->createView()
        ]);
    }



    #[Route('/compte/modifier-une-adresse/{id}', name: 'account_address_edit')]
    public function edit(Request $request, $id): Response
    {
        /** @var $user \App\Entity\User */
        $user = $this->getUser();

        $adresse = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$adresse || $adresse->getUser() != $user){
            return  $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $adresse);//on rempli le formulaire ici

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();
            return $this->redirectToRoute('account_address');
        }
        return $this->render('account/address_formulaire.html.twig',[
            'form' => $form->createView()
        ]);
    }


    #[Route('/compte/supprimer-une-adresse/{id}', name: 'account_address_delete')]
    public function delete($id): Response
    {
        /** @var $user \App\Entity\User */
        $user = $this->getUser();

        $adresse = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if($adresse && $adresse->getUser() == $user){
            $this->entityManager->remove($adresse);
            $this->entityManager->flush();
        }
        return  $this->redirectToRoute('account_address');
    }

}
