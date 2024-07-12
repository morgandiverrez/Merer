<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Formation\Profil;
use Aws\Ses\SesClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
   
    #[Route('/', name: 'index')]
    public function index( ): Response
    {
     

          return $this->redirectToRoute('profil_show');
    }
    #[Route('/login', name: 'login')]
    public function login( AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();

         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('login/index.html.twig', [
             'last_username' => $lastUsername,
             'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


    #[Route('/mentionlegale', name: 'mentionlegale')]
    public function mentionlegale()
    {   
        return $this->render('login/mentionlegale.html.twig', [
        ]);
    }

    #[Route('/download/politique_confidentialite', name: 'politique_confidentialite')]
    public function download()
    {
       
        $finaleFile = "build/document/politique_confidentialité.pdf";

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($finaleFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($finaleFile));
        readfile($finaleFile);



        return $this->redirectToRoute('index');
        
    }

    #[Route('/support', name: 'support')]
    public function support(Request $request, SesClient $ses)
    {
           if ($request->isMethod('post')) {
            $posts = $request->request->all();

            $sender_email ='no-reply@*****.net';
                $recipient_emails = ['diverrezm@gmail.com', 'numerique@*****.net'];

                $subject = 'Merer - Support';
                $plaintext_body = 'Support Merer' ;
                $char_set = 'UTF-8';
                $result = $ses->sendEmail([
                    'Destination' => [
                        'ToAddresses' => $recipient_emails,
                    ],
                    'ReplyToAddresses' => [$sender_email],
                    'Source' => $sender_email,
                    'Message' => [
                        'Body' => [
                            'Html' => [
                                'Charset' => $char_set,
                                'Data' =>$this->renderView('emails/support.html.twig',["posts" => $posts])
                            ],
                            'Text' => [
                                'Charset' => $char_set,
                                'Data' => $plaintext_body,
                            ],
                        ],
                        'Subject' => [
                            'Charset' => $char_set,
                            'Data' => $subject,
                        ],
                    ],
                
                ]);
            return $this->redirectToRoute('profil_show');
        }

        return $this->render('login/support.html.twig', [
        ]);
    }
}
