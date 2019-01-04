<?php

namespace App\Controller;

use App\Entity\SpellSchool;
use App\Form\SpellSchool3Type;
use App\Repository\SpellSchoolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/spell/school")
 */
class SpellSchoolController extends AbstractController
{
    /**
     * @Route("/", name="spell_school_index", methods={"GET"})
     */
    public function index(SpellSchoolRepository $spellSchoolRepository): Response
    {
        return $this->render('spell_school/index.html.twig', ['spell_schools' => $spellSchoolRepository->findAll()]);
    }

    /**
     * @Route("/new", name="spell_school_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $spellSchool = new SpellSchool();
        $form = $this->createForm(SpellSchool3Type::class, $spellSchool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($spellSchool);
            $entityManager->flush();

            return $this->redirectToRoute('spell_school_index');
        }

        return $this->render('spell_school/new.html.twig', [
            'spell_school' => $spellSchool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="spell_school_show", methods={"GET"})
     */
    public function show(SpellSchool $spellSchool): Response
    {
        return $this->render('spell_school/show.html.twig', ['spell_school' => $spellSchool]);
    }

    /**
     * @Route("/{id}/edit", name="spell_school_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SpellSchool $spellSchool): Response
    {
        $form = $this->createForm(SpellSchool3Type::class, $spellSchool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('spell_school_index', ['id' => $spellSchool->getId()]);
        }

        return $this->render('spell_school/edit.html.twig', [
            'spell_school' => $spellSchool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="spell_school_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SpellSchool $spellSchool): Response
    {
        if ($this->isCsrfTokenValid('delete'.$spellSchool->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($spellSchool);
            $entityManager->flush();
        }

        return $this->redirectToRoute('spell_school_index');
    }
}
