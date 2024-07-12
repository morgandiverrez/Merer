<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\User;
use Aws\Ses\SesClient;
use App\Form\ChangePasswordFormType;
use App\Entity\Comptability\Customer;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Comptability\CreateNewUserProfileForCustomerFormType;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/reset-password', name: 'passwordForgot')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;
    private EntityManagerInterface $entityManager;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, EntityManagerInterface $entityManager)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * Display & process form to send a email for create the user profil of the customer.
     */
    #[Route('/createNewUserProfileForCustomer/{customerID}', name: 'createNewUserProfileForCustomer')]
    #[IsGranted('ROLE_TRESO')]
    public function requestNewUser(Request $request, SesClient $ses, TranslatorInterface $translator, $customerID, UserPasswordHasherInterface $userPasswordHasher): Response
    {  
         $faker = Factory::create('fr_FR');
        $customer = $this->entityManager->getRepository(Customer::class)->findById($customerID)[0];

        $form = $this->createForm(CreateNewUserProfileForCustomerFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
               $user = new User;
                $user->setEmail($form->get('email')->getData());
                $user->setCustomer($customer);
                $encodedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $faker->sentence()
            );
            $user->setPassword($encodedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->processSendingPasswordCreateEmail(
                $translator,
                $ses,
                $user
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Display & process form to send a email for create the user profil of the editor.
     */
    #[Route('/createNewUserProfileForEditor/{editorID}', name: 'createNewUserProfileForEditor')]
    #[IsGranted('ROLE_TRESO')]
    public function requestNewUserForEditor(Request $request, SesClient $ses, TranslatorInterface $translator, $editorID, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $faker = Factory::create('fr_FR');
        $editor = $this->entityManager->getRepository(Editor::class)->findById($editorID)[0];

        $form = $this->createForm(CreateNewUserProfileForCustomerFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User;
            $user->setEmail($form->get('email')->getData());
            $user->setEditor($editor);
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $faker->sentence()
            );
            $user->setPassword($encodedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->processSendingPasswordCreateEmail(
                $translator,
                $ses,
                $user
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

   
    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, SesClient $ses, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $translator,
                $ses
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
      

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $translator, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('passwordForgotapp_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('passwordForgotapp_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, TranslatorInterface $translator,  SesClient $ses): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('passwordForgotapp_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
             
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('passwordForgotapp_forgot_password_request');
        }
        
        $sender_email = 'no-reply@*****.net';
         $recipient_emails = [$emailFormData];
        $subject = 'Merer - Reset Password';
        $plaintext_body = 'reset Password' ;
       
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
                    'Data' => $this->renderView('reset_password/email.html.twig',["resetToken" => $resetToken]),
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

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('passwordForgotapp_check_email');
    }


     private function processSendingPasswordCreateEmail(TranslatorInterface $translator, SesClient $ses, User $user): RedirectResponse
    {
      

       
     
        $resetToken = $this->resetPasswordHelper->generateResetToken($user);
      

        $sender_email = 'no-reply@*****.net';
         $recipient_emails = [$user->getEmail()];
        $subject = 'Merer - Reset Password';
        $plaintext_body = 'reset Password' ;
       
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
                    'Data' => $this->renderView('reset_password/emailNewUser.html.twig',["resetToken" => $resetToken]),
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

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('passwordForgotapp_check_email');
    }
}
