<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;


class InscriptionSortieController extends AbstractController
{
    #[Route('/inscription/sortie/{sortie}', name: 'app_inscription_sortie')]
    public function index(
        EntityManagerInterface         $entityManager,
        Sortie                         $sortie,
        NotifierInterface              $notifier


    ): Response
    {
        $participant = $this->getUser();
        if($sortie->getDateCloture() > 'now') {
            $inscrit = false;
            $collectionInscription = $participant->getInscription();
            foreach ($collectionInscription as $inscriptionSortie){
                if($sortie == $inscriptionSortie){
                    $inscrit = true;
                    }
                }
            if($inscrit){
                $participant->removeInscription($sortie);
                $notifier->send(new Notification('Votre désinscription a bien été enregistrée', ['browser']));
            }
            else{
                $participant->addInscription($sortie);
                $notifier->send(new Notification('Votre inscription est bien enregistrée', ['browser']));
            }

            $entityManager->persist($participant);
            $entityManager->flush();
            }

        else{
            $notifier->send(new Notification('La date de cloture est dépasée, il n\'est plus possible de modifier votre choix !', ['browser']));
        }
        return $this->redirectToRoute('sortie_index');
    }

}
