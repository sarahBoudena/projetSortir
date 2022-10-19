<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\AnnulerSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
                          NotifierInterface             $notifier,
                          Request                       $request
    ): Response
    {
        $organisateur = $this->getUser();
        if($sortie->getDateCloture() > 'now' && $sortie->getOrganisateur()===$organisateur) {
            if($sortie->getEtat()->getId() != 7){

                $form = $this->createForm(AnnulerSortieType::class, $sortie);
                $form->handleRequest($request);
                $sortie->setEtat($etatRepository->find(7));
                if ($form->isSubmitted() && $form->isValid()) {
                    if($sortie->getRaisonAbandon() != null){
                        $entityManager->persist($sortie);
                        $entityManager->flush();
                        $notifier->send(new Notification('La sortie a bien été annulée', ['browser']));
                        return $this->redirectToRoute('sortie_index');
                    }
                    $notifier->send(new Notification('Par respect pour tes collègues inscrits, il est nécessaire de renseigner la raison de l\'annulation.', ['browser']));
                }
                return $this->render('sortie/annulation.html.twig',["form"=>$form->createView()]);
            }
            if($sortie->getEtat()->getId() == 7)
                {$sortie->setEtat($etatRepository->find(2));
                $sortie->setRaisonAbandon('');
                $entityManager->persist($sortie);
                $entityManager->flush();
                $notifier->send(new Notification('La sortie a bien été restaurée', ['browser']));
                return $this->redirectToRoute('sortie_index');}
        }
        return $this->redirectToRoute('sortie_index');
    }
}

