<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/accueil", name="home")
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param User $user
     */
    public function index()
    {
        $user = $this->getUser();

        if (in_array('ROLE_LESSEE', $user->getRoles())) {
            return $this->redirectToRoute('property_index');
        }

        return $this->render('rent_release/pdf.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
