<?php

namespace App\Controller;

use App\Entity\CharacterClass;
use App\Form\CharacterClassType;
use App\Repository\CharacterClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/character/class")
 */
class CharacterClassController extends AbstractController
{
    /**
     * @Route("/", name="character_class_index", methods={"GET"})
     */
    public function index(CharacterClassRepository $characterClassRepository): Response
    {
        return $this->render('character_class/index.html.twig', ['character_classes' => $characterClassRepository->findAll()]);
    }

    /**
     * @Route("/new", name="character_class_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $characterClass = new CharacterClass();
        $form = $this->createForm(CharacterClassType::class, $characterClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($characterClass);
            $entityManager->flush();

            return $this->redirectToRoute('character_class_index');
        }

        return $this->render('character_class/new.html.twig', [
            'character_class' => $characterClass,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="character_class_show", methods={"GET"})
     */
    public function show(CharacterClass $characterClass): Response
    {
        return $this->render('character_class/show.html.twig', ['character_class' => $characterClass]);
    }

    /**
     * @Route("/{id}/edit", name="character_class_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CharacterClass $characterClass): Response
    {
        $form = $this->createForm(CharacterClassType::class, $characterClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('character_class_index', ['id' => $characterClass->getId()]);
        }

        return $this->render('character_class/edit.html.twig', [
            'character_class' => $characterClass,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="character_class_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CharacterClass $characterClass): Response
    {
        if ($this->isCsrfTokenValid('delete'.$characterClass->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($characterClass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('character_class_index');
    }
}
