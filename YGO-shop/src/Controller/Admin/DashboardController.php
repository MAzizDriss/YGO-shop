<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(DeckCrudController::class)->generateUrl();
        return $this->redirect($url);
        //return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('YGO Shop');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Cards', 'fas fa-list', Card::class);
        yield MenuItem::linkToCrud('Decks', 'fas fa-list', Deck::class);
        yield MenuItem::linkToCrud('Members', 'fas fa-list', Member::class);
        
    }
}
