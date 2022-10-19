<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'maj-etat-sorties-command',
    description: 'Met à jour l\'état des sorties en fonction de la date du jour',
)]
class MajEtatSortiesCommand extends Command
{
    protected static $defaultName = 'app:maj-etat-sorties-command';

    private EntityManagerInterface $manager;

    private SortieRepository $sortieRepository;

    private EtatRepository $etatRepository;

    public function __construct(
        SortieRepository $sortieRepository,
        EntityManagerInterface $manager,
        EtatRepository $etatRepository)
    {
        parent::__construct(self::$defaultName);

        $this->sortieRepository = $sortieRepository;
        $this-> etatRepository = $etatRepository;
        $this->manager = $manager;
    }


    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $etats = $this->etatRepository->findAll();
        $sorties = $this->sortieRepository->findAll();

        foreach ($sorties as $sortie){
             if (($sortie->getEtat()->getId() != 1) or ($sortie->getEtat()->getId() <= 6)) {
                $aujourdhui = date('Y-m-d  H:i:s');

                if(strtotime($sortie->getDateCloture()->format('Y-m-d  H:i:s')) > strtotime($aujourdhui)){
                    $sortie->setEtat($etats[1]);}

                if(strtotime($sortie->getDateCloture()->format('Y-m-d  H:i:s')) < strtotime($aujourdhui)
                            && strtotime($sortie->getDateDebut()->format('Y-m-d  H:i:s')) > strtotime($aujourdhui)){
                    $sortie->setEtat($etats[2]);}

                if(strtotime($sortie->getDateDebut()->format('Y-m-d  H:i:s')) >= date('Y-m-d  H:i:s', strtotime(-$sortie->getDuree() . ' i', strtotime(($aujourdhui)))) &&
                            date('Y-m-d  H:i:s', strtotime(-$sortie->getDuree().' minute', strtotime(($aujourdhui)))) > $sortie->getDateDebut()->format('Y-m-d  H:i:s')){
                            $sortie->setEtat($etats[3]);}

                if(strtotime(date('Y-m-d  H:i:s', strtotime(-$sortie->getDuree().' minute', strtotime(($aujourdhui))))) > strtotime($sortie->getDateDebut()->format('Y-m-d  H:i:s')) &&
                    strtotime(date('Y-m-d  H:i:s', strtotime('-1 month', strtotime(($aujourdhui))))) < strtotime($sortie->getDateDebut()->format('Y-m-d  H:i:s'))){
                     $sortie->setEtat($etats[4]);}

                if(strtotime(date('Y-m-d  H:i:s', strtotime('-1 month', strtotime(($aujourdhui))))) > strtotime($sortie->getDateDebut()->format('Y-m-d  H:i:s'))){
                            $sortie->setEtat($etats[5]);}
                }

                $this->manager->persist($sortie);
            }
        $this->manager->flush();
        $io->success('Votre commande est un franc succès');
        return Command::SUCCESS;
    }
}

