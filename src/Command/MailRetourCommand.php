<?php
//src/Command/MailRetourCommand.php

namespace App\Command;

use DateTime;
use App\Entity\Seance;
use App\Entity\Retour;
use Aws\Ses\SesClient;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(   
    name: 'send:email', 
    description: 'envoi des emails de rappel pour les formations de la journée',
    hidden: false,
    aliases: ['send:email']
)]
class MailRetourCommand extends Command
{
    //protected static $defaultName = 'send:email';

    /**
     * @var ContainerInterface
     */
    protected $container;

 

    public function setContainer(ContainerInterface $container): ?ContainerInterface
    { 
        $this->container = $container;
        $previous = $this->container;

        return $previous;
    }

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

 

    /**
     * @var SesClient
     */
    protected $ses;

  
    
    public function __construct(EntityManagerInterface $entityManager, SesClient $ses)
    {
          $this->entityManager = $entityManager;
          $this->ses = $ses;
          

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Command for send self email');
    }

    protected function renderView(string $view, array $parameters = []): string
    {
        if (! $this->container->has('twig')) {
            throw new \LogicException('You cannot use the "renderView" method if the Twig Bundle is not available. Try running "composer require symfony/twig-bundle".');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $seances = $this->entityManager->getRepository(Seance::class)->findAllByYear((new Datetime)->modify(" -1 day"), date("Y-m-d H:i"));
        
        foreach($seances as $seance){
            foreach($seance->getSeanceProfil() as $seanceProfil){
                $profil = $seanceProfil->getProfil();
                if(  $this->entityManager->getRepository(Retour::class)->findBy2ID($seance->getId(), $profil->getId()) == []){
                    $sender_email = '****@******';
                    $recipient_emails = [$profil->getUser()->getEmail()];
                    $subject = 'Merer - Retour de formation';
                    $plaintext_body = 'Retour de formation';
                    $char_set = 'UTF-8';
                    $result = $this->ses->sendEmail([
                        'Destination' => [
                            'ToAddresses' => $recipient_emails,
                        ],
                        'ReplyToAddresses' => [$sender_email],
                        'Source' => $sender_email,
                        'Message' => [
                            'Body' => [
                                'Html' => [
                                    'Charset' => $char_set,
                                    'Data' => " <p>Bonjour,</p>
                                                <p>Suite à la formation ".$seance->getName()." du ".$seance->getDateTime()->format('d/m/Y à H:i').".</p>
                                                <p>Veuillez répondre au formulaire de retour via ce <a href='https://15.236.191.187/retour/new/{{seance.getId()}}'>lien</a>.</p>
                                                <p>Lien pour vous connecter à la plateforme Merer <a href='https://15.236.191.187/'>Merer - ******</a>.</p>"
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
                }
            }
        }

      
        return 1;
    }
}
