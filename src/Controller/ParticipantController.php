<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Filesystem\Filesystem;




#[Route('/participant')]
class   ParticipantController extends AbstractController
{
    #[Route('/verif', name: 'verif_actif', methods: ['GET'])]
    public function verif(SessionInterface              $session): Response
    {
        if($this->getUser()->isActif()) {
            return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);}
        else{
            $session->set('message_deco', "Pourquoi tu fais ça ? Tu es déjà connecté...");
            return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);}
    }


    #[Route('/', name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository         $participantRepository,
                          NotifierInterface              $notifier): Response
    {
        if($this->getUser()->isAdministrateur()) {
            return $this->render('participant/index.html.twig', [
                'participants' => $participantRepository->findAll(),
            ]);
        }
        $notifier->send(new Notification('Petit coquinou, tu n\est pas administrateur, tu ne peux donc pas incrire de nouveaux utilisateurs', ['browser']));
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}', name: 'app_activer_utilisateur', methods: ['GET'])]
    public function admin(ParticipantRepository       $participantRepository,
                          NotifierInterface           $notifier,
                          Participant                 $participant): Response
    {
        if($this->getUser()->isAdministrateur()) {
            if ($participant->isActif()){
                $participant->setActif(false);
                $notifier->send(new Notification($participant->getPseudo().'est désactivé', ['browser']));
            }
            else{
                $participant->setActif(true);
                $notifier->send(new Notification($participant->getPseudo().'est desormais actif', ['browser']));
                }
            $participantRepository->save($participant, true);
            return $this->render('participant/index.html.twig', [
                'participants' => $participantRepository->findAll(),
            ]);
        }
        $notifier->send(new Notification('Petit coquinou, tu n\'est pas administrateur, tu ne peux donc pas incrire de nouveaux utilisateurs', ['browser']));
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/activer/{id}', name: 'app_affecter_role_admin', methods: ['GET'])]
    public function activer(ParticipantRepository       $participantRepository,
                            NotifierInterface           $notifier,
                            Participant                 $participant): Response
    {
        if($this->getUser()->isAdministrateur()) {
            if ($participant->isAdministrateur()){
                $participant->setAdministrateur(false);
                $notifier->send(new Notification($participant->getPseudo().'n\'est plus administrateur', ['browser']));
            }
            else{
                $participant->setAdministrateur(true);
                $notifier->send(new Notification($participant->getPseudo().'est desormais administrateur', ['browser']));
            }
            $participantRepository->save($participant, true);
            return $this->render('participant/index.html.twig', [
                'participants' => $participantRepository->findAll(),
            ]);
        }
        $notifier->send(new Notification('Petit coquinou, tu n\'est pas administrateur, tu ne peux donc pas incrire de nouveaux utilisateurs', ['browser']));
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request                     $request,
                        ParticipantRepository       $participantRepository,
                        NotifierInterface           $notifier

    ): Response
    {

        if($this->getUser()->isAdministrateur())
            {
                $participant = new Participant();
                $form = $this->createForm(ParticipantType::class, $participant);
                $participant->setUrlPhotoProfil('img/participant/photo_profil_defaut.png');
                $form->handleRequest($request);


                if ($form->isSubmitted() && $form->isValid()) {
                    $participantRepository->save($participant, true);
                    $notifier->send(new Notification('Le nouvel utilisateur a bien été inscrit', ['browser']));
                    return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
                }

                return $this->render('participant/new.html.twig', [
                    'participant' => $participant,
                    'form' => $form->createView(),
                ]);
            }
        $notifier->send(new Notification('Petit coquinou, tu n\'est pas administrateur, tu ne peux donc pas incrire de nouveaux utilisateurs', ['browser']));
        return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
            

    }

    #[Route('/{id}', name: 'app_participant_show', methods: ['GET'])]
    public function show(Participant                    $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/{id}/supp', name: 'supprimer_image', methods: ['GET', 'POST'])]
    public function removeImg(  ParticipantRepository         $participantRepository,
                                Participant                   $participant,
                                NotifierInterface             $notifier): Response
    {
        $ancienneImage = $participant->getUrlPhotoProfil();
        $participant->setUrlPhotoProfil('img/participant/photo_profil_defaut.png');
        if ($ancienneImage != 'img/participant/photo_profil_defaut.png'){
            $filesystem = new Filesystem();
            $filesystem->remove('path/to/file/file.pdf');$filesystem->remove($ancienneImage);
        }
        $participantRepository->save($participant, true);
        $notifier->send(new Notification('L\'image a bien été mise à jour', ['browser']));

        return $this->redirectToRoute('app_participant_edit', [
            'id' => $participant->getId(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participant_edit', methods: ['GET', 'POST'])]
    public function edit(Request                        $request,
                         Participant                    $participant,
                         UserPasswordHasherInterface    $userPasswordHasher,
                         ParticipantRepository          $participantRepository,
                         NotifierInterface              $notifier): Response
    {
        if($participant == $this->getUser()){

            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                    $participant->setMotDePasse(
                        $userPasswordHasher->hashPassword(
                            $participant, $form->get('plainPassword')->getData()
                        )
                    );

                    //Upload image

                $uploadedFile = ($form['imageFile']->getData());
                if($uploadedFile) {
                    $destination = $this->getParameter('kernel.project_dir') . '/public/img/participant';
                    $newFilename = 'img/participant/'.$participant->getPseudo().'.'.$uploadedFile->guessExtension();
                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );
                    $ancienneImage = $participant->getUrlPhotoProfil();
                    $participant->setUrlPhotoProfil($newFilename);
                    if ($ancienneImage != 'img/participant/photo_profil_defaut.png'){
                        $filesystem = new Filesystem();
                        $filesystem->remove('path/to/file/file.pdf');$filesystem->remove($ancienneImage);
                    }
                }

                $participantRepository->save($participant, true);
                $notifier->send(new Notification('La mise à jour de vos données a bien été effectuée', ['browser']));
                return $this->redirectToRoute('app_participant_show', ['id' =>$participant->getId()], Response::HTTP_SEE_OTHER, compact("participant"));}

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);}

        else{return $this->redirectToRoute('sortie_index');

        }

    }

    #[Route('/{id}', name: 'app_participant_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
