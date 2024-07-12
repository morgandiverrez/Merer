<?php

namespace App\Controller\Communication;

use App\Entity\Communication\Post;
use App\Form\Communication\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/post', name: 'post_')]
class PostController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    #[IsGranted('ROLE_COM')]
    public function showAll(EntityManagerInterface $entityManager, Request $request): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('Communication/post/showAll.html.twig', [
            'posts' => $posts,

        ]);
    }

    #[Route('/show/{postID}', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, $postID): Response
    {

        $post = $entityManager->getRepository(Post::class)->findById($postID)[0];
        if ($post->getEditor()->getUser() == $this->getUser() or $this->isGranted('ROLE_COM')) {
         
            return $this->render('Communication/post/show.html.twig', [
                'post' => $post,
            ]);
        } else return $this->redirectToRoute('account');
    }


    #[Route('/new', name: 'new')]
    #[IsGranted('ROLE_USER')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {   $editor = $this->getUser()->getEditor();
        if ($editor or $this->isGranted('ROLE_COM')) {
            
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->setEditor($editor);
                $entityManager->persist($post);
                
                $entityManager->flush();
                return $this->redirectToRoute('post_show', ['postID' => $post->getId()]);
            }

            return $this->render('Communication/post/new.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ]);
        }else return $this->redirectToRoute('account');
        
    }



    #[Route('/edit/{postID}', name: 'edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(EntityManagerInterface $entityManager, Request $request, $postID): Response
    {
        $post = $entityManager->getRepository(Post::class)->findById($postID)[0];
        if ($post->getEditor()->getUser() == $this->getUser() or $this->isGranted('ROLE_COM')) {
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->redirectToRoute('post_show', ['postID' => $postID]);
            }

            return $this->render('Communication/post/edit.html.twig', [
                'post' => $post,
                'form' => $form->createView(),

            ]);
        } else return $this->redirectToRoute('account');
    }

    #[Route('/delete/{postID}', name: 'delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, $postID): Response
    {

        $post = $entityManager->getRepository(Post::class)->findById($postID)[0];
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('post_showAll');
    }

    #[Route('/visible/{postID}', name: 'cloture')]
    #[IsGranted('ROLE_USER')]
    public function visible(EntityManagerInterface $entityManager, $postID): Response
    {
        $post = $entityManager->getRepository(post::class)->findById($postID)[0];
        if ($post->getEditor()->getUser() == $this->getUser() or $this->isGranted('ROLE_COM')) {
            $post->setVisible(true);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('post_showAll');
        }else{
            return $this->redirectToRoute('account');
        }
    }

    #[Route('/unvisible/{postID}', name: 'uncloture')]
    #[IsGranted('ROLE_USER')]
    public function unvisible(EntityManagerInterface $entityManager, $postID): Response
    {
        $post = $entityManager->getRepository(post::class)->findById($postID)[0];
        if($post->getEditor()->getUser() == $this->getUser() or $this->isGranted('ROLE_COM')){
            $post->setVisible(false);
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('post_showAll');
        }else{
            return $this->redirectToRoute('account');
        }
    }
}
