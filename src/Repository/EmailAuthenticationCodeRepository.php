<?php

namespace App\Repository;

use App\Entity\EmailAuthenticationCode;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailAuthenticationCode>
 *
 * @method EmailAuthenticationCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailAuthenticationCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailAuthenticationCode[]    findAll()
 * @method EmailAuthenticationCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailAuthenticationCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailAuthenticationCode::class);
    }

    public function add(EmailAuthenticationCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmailAuthenticationCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLastValidByEmail(string $email): ?EmailAuthenticationCode
    {
        return $this->createQueryBuilder('eac')
            ->andWhere('eac.email = :email')
            ->setParameter('email', $email)
            ->andWhere('eac.validTo >= :now')
            ->setParameter('now', new DateTimeImmutable())
            ->orderBy('eac.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteByEmail(string $email): void
    {
        $this->createQueryBuilder('eac')
            ->andWhere('eac.email = :email')
            ->setParameter('email', $email)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
