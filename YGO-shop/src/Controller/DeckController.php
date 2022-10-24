<?php

namespace App\Controller;

use App\Entity\Deck;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur Deck
 * @Route("/deck")
 */

class DeckController extends AbstractController
{


    /**
     * @Route("/", name = "home", methods="GET")
     */
    public function indexAction()
    {
        return $this->render('deck/index.html.twig',
        [ 'welcome' => "Bonne utilisation de la todo list" ]
    );
    }




    #[Route('/list', name: 'app_deck')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $entityManager= $doctrine->getManager();
        $decks = $entityManager->getRepository(Deck::class)->findAll();
        dump($decks);

        return $this->render('deck/list.html.twig', [
            'decks'=> $decks,
        ]);
    }

    /**
 * Show a deck
 * 
 * @Route("/deck/{id}", name="deck_show", requirements={"id"="\d+"})
 *    note that the id must be an integer, above
 *    
 * @param Integer $id
 */
public function showAction(ManagerRegistry $doctrine, $id): Response
{
    $DeckRepo = $doctrine->getRepository(Deck::class);
    $deck = $DeckRepo->find($id);
    

    if (!$deck) {
        throw $this->createNotFoundException('The Deck does not exist');
    }
    $d_cards = $deck->getCards();

    return $this->render('deck/show.html.twig',
[
    'deck'=> $deck,
    'd_cards'=>$d_cards
]);
}

}
