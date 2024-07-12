<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/signature', name: 'signature_')]

class SignatureController extends AbstractController
{
    #[Route('/CROUS', name: 'CROUS')]
    #[IsGranted('ROLE_USER')]
    public function CROUS(): Response
    {
        return $this->render('signature/CROUS.html.twig');
    }

    #[Route('/centrauxUBO', name: 'centrauxUBO')]
    #[IsGranted('ROLE_USER')]
    public function centrauxUBO(): Response
    {
        return $this->render('signature/centrauxUBO.html.twig');
    }

    #[Route('/BF', name: 'BF')]
    #[IsGranted('ROLE_BF')]
    public function BF(): Response
    {
        return $this->render('signature/BF.html.twig');
    }
}

