<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findSortieWithLieu():array {
        $reqSQL ="
            SELECT s FROM APP\Entity\Sortie s
            INNER JOIN \APP\Entity\Lieu l
            ON s.lieu_id = l.id
        ";
        $req = $this->getEntityManager()->createQuery($reqSQL);
        return $req->getResult();
    }




//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
    public function findByFilter($site, $nom, $dateDebut, $dateFin, $organisateur, $inscrit, $nonInscrit, $passe): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->andWhere('s.site = :site')
       ->setParameter('site', $site);

//            if($organisateur!=null OR $inscrit!=null OR $nonInscrit!=null){
//                $qb->join('', 'p' );
//            }

            if($organisateur!=null){
                $qb->andWhere('s.organisateur = :orga' );
                $qb->setParameter('orga', $organisateur);
            }

            if ($inscrit!=null){
                $qb->join('s.participants','p');
                $qb->andWhere('p.id = :user');
                $qb->setParameter('user', $inscrit);
            }

            if ($nonInscrit!=null){
                $qb->join('s.participants','p');
                $qb->andWhere('p.id <> :user' );
                $qb->setParameter('user', $nonInscrit);
            }

            if ($passe!=null){
                $qb->andWhere('s.etat = :etat');
                $qb->setParameter('etat', $passe);
            }

            if ($nom!=null){
                $qb->andWhere("s.nomSortie LIKE :nom");
                $qb->setParameter('nom', $nom);
            }

            if ($dateDebut!=null && $dateFin!=null){
                $qb->andWhere('s.dateDebut BETWEEN :debut AND :fin');
                $qb->setParameter('debut', $dateDebut);
                $qb->setParameter('fin', $dateFin);
            }

            $req = $qb->getQuery();


            return $req->getResult();
    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
