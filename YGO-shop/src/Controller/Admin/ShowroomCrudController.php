<?php

namespace App\Controller\Admin;

use App\Entity\Showroom;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ShowroomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Showroom::class;
    }

    public function configureFields(string $pageName): iterable
    {

    return [
        IdField::new('id')->hideOnForm(),
        TextField::new('sr_name'),
        AssociationField::new('owner'),
        BooleanField::new('published')
        ->onlyOnForms(),
        TextField::new('description'),

        AssociationField::new('cards')
        ->onlyOnForms()
        // on ne souhaite pas gérer l'association entre les
        // [objets] et la [galerie] dès la crétion de la
        // [galerie]
        ->hideWhenCreating()
        ->setTemplatePath('admin/fields/Deck_Cards.html.twig')
        // Ajout possible seulement pour des [objets] qui
        // appartiennent même propriétaire de l'[inventaire]
        // que le [createur] de la [galerie]
        ->setQueryBuilder(
            function (ORMQueryBuilder $queryBuilder) {
            // récupération de l'instance courante de [galerie]
            $currentSr = $this->getContext()->getEntity()->getInstance();
            $owner = $currentSr->getOwner();
            $memberId = $owner->getId();
            // charge les seuls [objets] dont le 'owner' de l'[inventaire] est le [createur] de la galerie
            $queryBuilder->leftJoin('entity.deck', 'i')
                ->leftJoin('i.member', 'm')
                ->andWhere('m.id = :member_id')
                ->setParameter('member_id', $memberId);    
            return $queryBuilder;
            }
           ),
    ];
    }
}
