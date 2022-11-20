<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\Member;
use App\Form\DeckType;
use App\Repository\DeckRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/deck')]
class DeckController extends AbstractController
{
    #[Route('/', name: 'app_deck_index', methods: ['GET'])]
    public function index(DeckRepository $deckRepository): Response
    {
        $user = $this->getUser();
        $decks = array();
        if($user){
            $member = $user->getMember();
            //The IDE signals this line as an error but thankfully it does work 

            $decks = $deckRepository->findBy([
                    'member' => $member
            ]);
        }
        if ($this->isGranted('ROLE_ADMIN')) {
            $decks = $deckRepository->findAll();
        }
        return $this->render('deck/index.html.twig', [
            'decks' => $decks,
        ]);
    }

    /**
    * @Route("/new/{id}", name="app_deck_new", methods={"GET", "POST"})
    */
    
    public function new(Request $request, DeckRepository $deckRepository, Member $member): Response
    {
        $deck = new Deck();
        $deck->setMember($member);
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deckRepository->add($deck, true);

            $this->addFlash('message', 'bien ajouté');
            return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deck/new.html.twig', [
            'deck' => $deck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deck_show', methods: ['GET'])]
    public function show(Deck $deck): Response
    {
        return $this->render('deck/show.html.twig', [
            'deck' => $deck,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deck_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deck $deck, DeckRepository $deckRepository): Response
    {
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deckRepository->add($deck, true);

            $this->addFlash('message', 'bien modifié');
            return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('deck/edit.html.twig', [
            'deck' => $deck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deck_delete', methods: ['POST'])]
    public function delete(Request $request, Deck $deck, DeckRepository $deckRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deck->getId(), $request->request->get('_token'))) {
            $deckRepository->remove($deck, true);
            $this->addFlash('message', 'bien supprimé');
        }

        return $this->redirectToRoute('app_deck_index', [], Response::HTTP_SEE_OTHER);
    }
}
