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
        $db = $this->createQueryBuilder('s');

            if($organisateur!=null OR $inscrit!=null OR $nonInscrit!=null){
                $db->join('s.participants', 'p' );
            }

            if($organisateur!=null){
                $db->andWhere('s.organisateur = orga' );
                $db->setParameter('orga', $organisateur);
            }

            if ($inscrit!=null){
                $db->andWhere('p.inscription = user' );
                $db->setParameter('user', $inscrit);
            }

            if ($nonInscrit!=null){
                $db->andWhere('p.inscription != user1 AND p.organisateur != user2' );
                $db->setParameter('user1', $nonInscrit);
                $db->setParameter('user2', $nonInscrit);
            }

            if ($passe!=null){
                $db->andWhere('s.etat = etat');
                $db->setParameter('etat', $passe);
            }

            if ($nom!=null){
                $db->andWhere('s.nomSortie LIKE nom');
                $db->setParameter('nom', $nom);
            }

            if ($dateDebut!=null && $dateFin!=null){
                $db->andWhere('s.dateDebut BETWEEN debut AND fin');
                $db->setParameter('debut', $dateDebut);
                $db->setParameter('fin', $dateFin);
            }

            $db->andWhere('s.site = site')
            ->setParameter('site', $site)

            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();

            return $db;
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
