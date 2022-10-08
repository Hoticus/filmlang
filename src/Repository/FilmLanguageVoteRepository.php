<?php

namespace App\Repository;

use App\Entity\FilmLanguageVote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FilmLanguageVote>
 *
 * @method FilmLanguageVote|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilmLanguageVote|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilmLanguageVote[]    findAll()
 * @method FilmLanguageVote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmLanguageVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilmLanguageVote::class);
    }

    public function add(FilmLanguageVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FilmLanguageVote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FilmLanguageVote[] Returns an array of FilmLanguageVote objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FilmLanguageVote
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
