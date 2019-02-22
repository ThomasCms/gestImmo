<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use App\Service\PdfUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/proprietes")
 * @IsGranted("ROLE_USER")
 */
class PropertyController extends AbstractController
{
    /**
     * @Route("/", name="property_index", methods={"GET"})
     * @param PropertyRepository $propertyRepository
     * @return Response
     */
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('property/index.html.twig', [
            'properties' => $propertyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajouter", name="property_new", methods={"GET","POST"})
     * @param Request $request
     * @param PdfUploader $pdfUploader
     * @return Response
     */
    public function new(Request $request, PdfUploader $pdfUploader): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $property->getPdfFile();

            if (isset($pdfFile)) {
                $fileName = $pdfUploader->uploadPdf($pdfFile);

                $property->setPdfFile($fileName);
            }

            $property->setUserProperty($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($property);
            $entityManager->flush();

            $this->addFlash('success', 'Votre propriétée a été enregistrée');

            return $this->redirectToRoute('property_index');
        }

        return $this->render('property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="property_show", methods={"GET"})
     * @param Property $property
     * @return Response
     */
    public function show(Property $property): Response
    {
        if (!$this->isGranted('SHOW', $property)) {
            $this->addFlash('danger', 'Vous n\'etes pas autorisé à effectuer cette action.');

            return $this->redirectToRoute('property_index');
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
        ]);
    }

    /**
     * @Route("/{id}/editer", name="property_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Property $property
     * @return Response
     */
    public function edit(Request $request, Property $property): Response
    {
        if (!$this->isGranted('EDIT', $property)) {
            $this->addFlash('danger', 'Vous n\'etes pas autorisé à effectuer cette action.');

            return $this->redirectToRoute('property_index');
        }

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre propriétée a été modifiée');

            return $this->redirectToRoute('property_index', [
                'id' => $property->getId(),
            ]);
        }

        return $this->render('property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="property_delete", methods={"DELETE"})
     * @param Request $request
     * @param Property $property
     * @return Response
     */
    public function delete(Request $request, Property $property): Response
    {
        if (!$this->isGranted('DELETE', $property)) {
            $this->addFlash('danger', 'Vous n\'etes pas autorisé à effectuer cette action.');

            return $this->redirectToRoute('property_index');
        }

        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($property);
            $entityManager->flush();

            $this->addFlash('success', 'Votre propriétée a été supprimée');
        }

        return $this->redirectToRoute('property_index');
    }
}
