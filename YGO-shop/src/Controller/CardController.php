<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card')]
class CardController extends AbstractController
{
    #[Route('/', name: 'app_card_index', methods: ['GET'])]
    public function index(CardRepository $cardRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $cards = $cardRepository->findAll();
        }
        else {
            //The IDE signals this line as an error but thankfully it does work 
            $member = $this->getUser()->getMember();
            // $member_decks = $member->getDecks();
            // $cards= array();

            // foreach($member_decks as $deck){
            //      $cards = array_merge(
            //         $cards,
            //         $cardRepository->findBy(
            //                 ['deck' => $deck])
            //         );}
            $cards = $cardRepository->findMemberCards($member);
        }

        return $this->render('card/index.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/new', name: 'app_card_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CardRepository $cardRepository): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardRepository->add($card, true);
            $this->addFlash('message', 'bien ajouté');
            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/new.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_show', methods: ['GET'])]
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardRepository->add($card, true);

            $this->addFlash('message', 'bien modifié');
            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/edit.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card, CardRepository $cardRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $cardRepository->remove($card, true);
            $this->addFlash('message', 'bien supprimé');
        }

        return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
    }
}
