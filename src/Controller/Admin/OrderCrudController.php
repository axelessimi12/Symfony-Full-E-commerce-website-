<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;
    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator){
            $this->entityManager=$entityManager;
            $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation','préparation en cours', 'fas fa-truck')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery','livraison en cours', 'fas fa-box-open')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail',$updatePreparation)
            ->add('detail',$updateDelivery)
            ->add('index','detail');
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);

        $this->entityManager->flush();
        $this->addFlash('notice',"<span style='color: orange;'><strong>La commande ".$order->getReference()." <u>est en cours de livraison.</u></strong></span>" );

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);

        $this->entityManager->flush();
        $this->addFlash('notice',"<span style='color: greenyellow;'><strong>La commande ".$order->getReference()." <u>est en cours de préparation.</u></strong></span>" );

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.fullname', 'Utilisateur'),
            TextEditorField::new('delivery','Adresse de livraison')->onlyOnDetail(),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('total','Total produit')->setCurrency('USD'),
            MoneyField::new('carrierPrice','Frais de port')->setCurrency('USD'),
            ChoiceField::new('state')->setChoices([
                'non payée' => 0,
                'payée' => 1,
                'préparation en cours' => 2,
                'livraison en cours' => 3,
            ]),
            ArrayField::new('orderDetails', 'Produit(s) acheté(s)')->hideOnIndex(),


        ];
    }

}
