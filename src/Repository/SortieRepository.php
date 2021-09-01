<?php

namespace App\Repository;

use App\data\RechercheData;
use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
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

    /**
     * @return Sortie Returns a Sortie object
     */
    public function findById($id) : Sortie
    {
        $sortie = new Sortie();

        $array = $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        foreach ($array as $s){
            $sortie = $s;
        }

        return $sortie;
    }

    public function trouveData(RechercheData $recherche,
                               Participant $user)
    {

        $query = $this
            ->createQueryBuilder('s')
            ->select('c','s')
            ->join('s.campus','c')
            ->join('s.etat', 'e')
            ->addSelect('e');

        $query = $query
            ->andWhere('e.id = 8 OR e.id =9 OR e.id =11');



        if(!empty($recherche->getQ())) {
            $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$recherche->getQ()}%");
        }

        if (!empty($recherche->getCampus())) {
            $query
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $recherche->getCampus());
        }

        if (!empty($recherche->getDateHeureDebut())) {
            $query
                ->andWhere('s.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', $recherche->getDateHeureDebut());
        }

        if (!empty($recherche->getDateCloture())) {
            $query
                ->andWhere('s.dateLimiteInscription <= :dateLimiteInscription')
                ->setParameter('dateLimiteInscription', $recherche->getDateCloture());
        }


        if($recherche->isOrganisateur()) {
            $query
                ->andWhere('s.organisateur = :user')
                ->setParameter('user', $user);
        }

        if($recherche->isInscrit()) {
            $query
                ->andWhere(':user MEMBER OF s.inscrits')
                ->setParameter('user', $user);
        }

        if($recherche->isNotInscrit()) {
            $query
                ->andWhere(':user NOT MEMBER OF s.inscrits')
                ->setParameter('user', $user);
        }

        if(!empty($recherche->isPassee())) {
            $now = new DateTime();
            $query
                ->andWhere('s.dateHeureDebut <= :now')
                ->setParameter('now', $now);

        }


        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
