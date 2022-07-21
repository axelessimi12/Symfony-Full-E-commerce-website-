<?php

namespace App\Classe;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart{

    private $requestStack;
    private $entityManager;


    public function __construct(RequestStack $requestStack,EntityManagerInterface $entityManager){
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;

    }

    public function add($id){
        $session = $this->requestStack->getSession();

        $cart = $session->get('cart',[]);// the second argument is the value returned when the attribute doesn't exist

        if(!empty($cart[$id])){
            $cart[$id]++;//on incremente la quantité
        }
        else{
            $cart[$id]=1;
        }

        $session->set('cart', $cart);
    }

    public function get(){
        $session = $this->requestStack->getSession();
        return $session->get('cart');
    }

    public function remove(){
        $session = $this->requestStack->getSession();
        return $session->remove('cart');
    }

    public function delete($id){
        $session = $this->requestStack->getSession();

        $cart = $session->get('cart',[]);// the second argument is the value returned when the attribute doesn't exist
        unset($cart[$id]);//suppression du produit du panier
        return  $session->set('cart', $cart);//update quantity
    }

    public function decrease($id){//Dimunition de une unité

        $session = $this->requestStack->getSession();
        $cart = $session->get('cart',[]);// the second argument is the value returned when the attribute doesn't exist
        if($cart[$id] > 1){
            $cart[$id]--;//on deiminue la quantité de 1
        }
        else{
            unset($cart[$id]);//suppression du produit du panier
        }
        return  $session->set('cart', $cart);//update quantity
    }

    public function getFull(){

        $cartComplete = [];
        if($this->get()){
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$product_object){
                    $this->delete($id);
                    continue; //en cas d'injection dans l'url d'un id inexistant indique a php de sortir de la boucle et passer au produit existant
                }
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;

    }
}

?>