<?php

namespace App\Controller;

use App\Entity\Card;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur Card
 * @Route("/card", name="CardController")
 */

class CardController extends AbstractController
{


    /**
     * @Route("/", name = "home", methods="GET")
     */

    public function indexAction()
    {
        return $this->render('card/index.html.twig',
        [ 'welcome' => "Bonne utilisation de la Card list" ]
    );
    }




    #[Route('/list', name: 'list_card')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $entityManager= $doctrine->getManager();
        $cards = $entityManager->getRepository(Card::class)->findAll();
        dump($cards);

        return $this->render('card/list.html.twig', [
            'cards'=> $cards,
        ]);
    }

    /**
 * Show a deck
 * 
 * @Route("/{id}", name="card_show", requirements={"id"="\d+"})
 *    note that the id must be an integer, above
 *    
 * @param Integer $id
 */
public function showAction(ManagerRegistry $doctrine, $id): Response
{
    $CardRepo = $doctrine->getRepository(Card::class);
    $card = $CardRepo->find($id);
    

    if (!$card) {
        throw $this->createNotFoundException('The Card does not exist');
    }


    return $this->render('card/show.html.twig',
[
    'card' => $card
]);
}

}
