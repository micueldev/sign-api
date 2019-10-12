<?php

namespace CommandBundle\Command\Notification;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ServiceBundle\Service\Util\Constante;
use ServiceBundle\Service\User\AlertService;

class EndAlertCommand extends ContainerAwareCommand
{
    public function configure()  
    {
        $this
            // El nombre del comando ("php bin/console app:CrearTxt")
            ->setName('app:Notification_EndAlert')

            // La descripcion corta del comando mientras se ejecuta "php app/console list"
            ->setDescription('Comando para crear hilo de una alerta y luego de un tiempo terminarlo.')

            // Se agrega los argumentos que necesitara el comando
            ->addArgument('idUser', InputArgument::REQUIRED, 'Id del usuario que emite la alerta')

            // La descripcion completa del comando cuando se ejecuta la opcion "--help"
            ->setHelp('Este comando crea un hilo con el id del usuario para terminarlo.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {   
        try{            
            $idUser = $input->getArgument('idUser');
            
            while(True){
                $em = $this->getContainer()->get('doctrine')->getEntityManager();

                $alert = $em->getRepository('EntityBundle:Item\Alert')->findOneBy(['user'=>$idUser,'isActive'=>True]);
                $alert ? : die();

                $em->refresh($alert);
                $timeEnd = $alert->getFT()->getTimestamp(); //Tiempo real donde debe terminar la alerta
                $timeSleep = $timeEnd-date_create()->getTimestamp();
                if($timeSleep>0){
                    unset($alert);
                    unset($em);                    
                    sleep($timeSleep); //dormimos por el tiempo restante que debe acabar la alerta
                }else{
                    $alert->setIsActive(False);

                    $user = $em->getRepository('EntityBundle:User\User')->findOneById($idUser);
                    $user->setIsAlert(False);
                    $em->flush();
                    $alertService = new AlertService($em);
                    $alertService->EndAlertNotification($idUser,$alert->getAUserAlert());
                    die();
                }
            }
        }
        catch (\Exception $e) {
            $error = $e->getMessage();
            $output->writeln('Notification_EndAlert" ->\nError "'.$error."'.");
        }
    }
}