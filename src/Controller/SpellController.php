<?php

namespace App\Controller;

use App\Entity\Spell;
use App\Form\Spell4Type;
use App\Repository\SpellRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/spell")
 */
class SpellController extends AbstractController
{
    /**
     * @Route("/", name="spell_index", methods={"GET"})
     */
    public function index(SpellRepository $spellRepository): Response
    {
        return $this->render('spell/index.html.twig', ['spells' => $spellRepository->findAll()]);
    }

    /**
     * @Route("/new", name="spell_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $spell = new Spell();
        $form = $this->createForm(Spell4Type::class, $spell);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($spell);
            $entityManager->flush();

            return $this->redirectToRoute('spell_index');
        }

        return $this->render('spell/new.html.twig', [
            'spell' => $spell,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="spell_show", methods={"GET"})
     */
    public function show(Spell $spell): Response
    {
        return $this->render('spell/show.html.twig', ['spell' => $spell]);
    }

    /**
     * @Route("/{id}/edit", name="spell_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Spell $spell): Response
    {
        $form = $this->createForm(Spell4Type::class, $spell);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('spell_index', ['id' => $spell->getId()]);
        }

        return $this->render('spell/edit.html.twig', [
            'spell' => $spell,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="spell_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Spell $spell): Response
    {
        if ($this->isCsrfTokenValid('delete'.$spell->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($spell);
            $entityManager->flush();
        }

        return $this->redirectToRoute('spell_index');
    }
}
