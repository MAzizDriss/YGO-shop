<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Member;
use App\Entity\Showroom;
use App\Form\ShowroomType;
use App\Repository\ShowroomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/showroom')]
class ShowroomController extends AbstractController
{
    #[Route('/', name: 'app_showroom_index', methods: ['GET'])]
    public function index(ShowroomRepository $showroomRepository): Response
    {
        return $this->render('showroom/index.html.twig', [
            'showrooms' => $showroomRepository->findBy(['published' => true]),
            // 'showrooms' => $showroomRepository->findAll(),
            
        ]);
    }

        /**
    * @Route("/new/{id}", name="app_showroom_new", methods={"GET", "POST"})
    */
    public function new(Request $request, ShowroomRepository $showroomRepository, Member $member): Response
    {
        $showroom = new Showroom();
        $member->addShowroom($showroom);
        $form = $this->createForm(ShowroomType::class, $showroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $showroomRepository->add($showroom, true);

            $this->addFlash('message', 'bien ajouté');
            return $this->redirectToRoute('app_showroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('showroom/new.html.twig', [
            'showroom' => $showroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_showroom_show', methods: ['GET'])]
    public function show(Showroom $showroom): Response
    {
        return $this->render('showroom/show.html.twig', [
            'showroom' => $showroom,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_showroom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Showroom $showroom, ShowroomRepository $showroomRepository): Response
    {
        $form = $this->createForm(ShowroomType::class, $showroom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $showroomRepository->add($showroom, true);

            $this->addFlash('message', 'bien modifié');
            return $this->redirectToRoute('app_showroom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('showroom/edit.html.twig', [
            'showroom' => $showroom,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_showroom_delete', methods: ['POST'])]
    public function delete(Request $request, Showroom $showroom, ShowroomRepository $showroomRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$showroom->getId(), $request->request->get('_token'))) {
            $showroomRepository->remove($showroom, true);
            $this->addFlash('message', 'bien supprimé');
        }

        return $this->redirectToRoute('app_showroom_index', [], Response::HTTP_SEE_OTHER);
    }




 /**
     * @Route("/{showroom_id}/card/{card_id}", name="app_showroom_card_show", methods={"GET"})
     * @ParamConverter("showroom", options={"id" = "showroom_id"})
     * @ParamConverter("card", options={"id" = "card_id"})
*/
    public function cardShow(Showroom $showroom, Card $card): Response
    {
    if(! $showroom->getCards()->contains($card)) {
        throw $this->createNotFoundException("Couldn't find such a card in this showroom!");
    }

    if(! $showroom->isPublished()) {
        throw $this->createAccessDeniedException("You cannot access the requested ressource!");
    }

    return $this->render('showroom/card_show.html.twig', [
        'card' => $card,
          'showroom' => $showroom
      ]);
    }



}
