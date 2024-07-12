<?php

namespace App\Controller\Communication;

use App\Entity\Communication\Editor;
use App\Form\Communication\EditorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/editor', name: 'editor_')]
class EditorController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_COM')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $editors = $entityManager->getRepository( Editor::class)->findAll();


        return $this->render('Communication/editor/showAll.html.twig', [
            'editors' => $editors,

        ]);
    }

    #[Route('/show/{editorID}', name: 'show')]
    #[IsGranted('ROLE_COM')]
    public function show(EntityManagerInterface $entityManager, $editorID): Response
    {

        $editor = $entityManager->getRepository(Editor::class)->findById($editorID)[0];

        return $this->render('Communication/editor/show.html.twig', [
            'editor' => $editor,

        ]);
    }
    
    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_COM')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $editor = new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager->persist($editor);
            $entityManager->flush();
            return $this->redirectToRoute('editor_show', ['editorID' => $editor->getId()]);
        }
        return $this->render('Communication/editor/new.html.twig', [
            'editor' => $editor,
            'form' => $form->createView(),


        ]);
    }

    #[Route('/edit/{editorID}', name: 'edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $editorID): Response
    {

        $editor = $entityManager->getRepository(Editor::class)->findById($editorID)[0];
        if (($editor->getUser() == $this->getUser() or $this->isGranted("ROLE_COM")) ) {
            
         
            $form = $this->createForm(EditorType::class, $editor);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($editor);
                $entityManager->flush();
                return $this->redirectToRoute('editor_show', ['editorID' => $editorID]);
            }

            return $this->render('Communication/editor/edit.html.twig', [
                'editor' => $editor,
                'form' => $form->createView(),

            ]);
        } else {
            return $this->redirectToRoute('account');
        }
    }


    #[Route('/delete/{editorID}', name: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(EntityManagerInterface $entityManager, $editorID): Response
    {

        $editor = $entityManager->getRepository(Editor::class)->findById($editorID)[0];
        $entityManager->remove($editor);
        $entityManager->flush();


        return $this->redirectToRoute('editor_showAll', []);
    }

  
}
