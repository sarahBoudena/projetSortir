<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;

class ChangerEtatMeSortiesController extends AbstractController
{
    #[Route('/changer/etat/mes/sorties/{sortie}', name: 'app_annuler_sortie')]
    public function index(EntityManagerInterface        $entityManager,

                          Sortie                        $sortie,
                          EtatRepository                $etatRepository,
                          NotifierInterface             $notifier
    ): Response
    {
        $organisateur = $this->getUser();

        if($sortie->getDateCloture() > 'now' && $sortie->getOrganisateur()===$organisateur) {
            $sortie->setEtat($etatRepository->find(7));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $notifier->send(new Notification('La sortie a bien été annulée', ['browser']));
        }
        return $this->redirectToRoute('sortie_index');
    }
}
