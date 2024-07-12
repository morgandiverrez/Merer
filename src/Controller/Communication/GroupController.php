<?php

namespace App\Controller\Communication;

use App\Entity\Communication\Group;
use App\Form\Communication\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/group', name: 'group_')]
class GroupController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_COM')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $groups = $entityManager->getRepository(Group::class)->findAll();

        return $this->render('Communication/group/showAll.html.twig', [
            'groups' => $groups,

        ]);
    }

  

    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_COM')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirectToRoute('group_showAll', );
        }

        return $this->render('Communication/group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),


        ]);
    }



    #[Route('/edit/{groupID}', name: 'edit')]
    #[IsGranted('ROLE_COM')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $groupID): Response
    {
        $group = $entityManager->getRepository(Group::class)->findById($groupID)[0];
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirectToRoute('group_showAll', );
        }

        return $this->render('Communication/group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/delete/{groupID}', name: 'delete')]
    #[IsGranted('ROLE_COM')]
    public function delete(EntityManagerInterface $entityManager, $groupID): Response
    {

        $group = $entityManager->getRepository(Group::class)->findById($groupID)[0];
        $entityManager->remove($group);
        $entityManager->flush();

        return $this->redirectToRoute('group_showAll');
    }
}
