<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_index')]
    public function index(
        SortieRepository $repository
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $sorties = $repository->findAll();
        return $this->render('sortie/index.html.twig', [
            "sorties"=>$sorties
        ]);
    }

    #[Route('/ajout', name: 'sortie_ajout')]
    public function ajout(
        EtatRepository $etatRepository,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $etat = $etatRepository->findOneBy(array('id'=>1));
            if (!$this->getUser()) {
                return $this->redirectToRoute('app_login');
            }
            $pseudo = $this->getUser()->getUserIdentifier();
            $organisateur = $participantRepository->findOneBy(array('pseudo'=>$pseudo));
            $sortie->setOrganisateur($organisateur);
            $sortie->setSite($organisateur->getSite());
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->render('sortie/index.html.twig');
        }
        return $this->render('sortie/ajout.html.twig', [
            "form"=>$form->createView()
        ]);
    }

    #[Route('/details/{id}', name: 'details_index',
                             requirements: ['id' => '\d+']
    )]
    public function details(
        Sortie $id
    ): Response
    {
        return $this->render('sortie/details.html.twig', [
            "sortie" =>$id
        ]);
    }
}
