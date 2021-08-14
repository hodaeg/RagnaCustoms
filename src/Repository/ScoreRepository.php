<?php

namespace App\Repository;

use App\Entity\Score;
use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Score|null find($id, $lockMode = null, $lockVersion = null)
 * @method Score|null findOneBy(array $criteria, array $orderBy = null)
 * @method Score[]    findAll()
 * @method Score[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Score::class);
    }

    // /**
    //  * @return Score[] Returns an array of Score objects
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
    public function findOneBySomeField($value): ?Score
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findBySeasonDiffHash(?Season $season, $difficulty, $hash)
    {
        $qb = $this->createQueryBuilder("s")
            ->where("s.difficulty = :difficulty")
            ->andWhere("s.hash = :hash")
            ->setParameter("difficulty", $difficulty)
            ->setParameter("hash", $hash);
        if ($season !== null) {
            $qb->andWhere("s.season = :season")
                ->setParameter("season", $season);
        }
        $qb->orderBy('s.score','desc');
        return $qb->getQuery()->getResult();
    }

}
