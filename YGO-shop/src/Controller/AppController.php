<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AppController extends AbstractController
{
    /**
 * @Route("/", name = "home", methods="GET")
 * @IsGranted("ROLE_USER")
 */
    public function index(): Response
    {
        $member = $this->getUser()->getMember();
        return $this->render('app/index.html.twig', [
            'member' => $member,
        ]);
    }
}
