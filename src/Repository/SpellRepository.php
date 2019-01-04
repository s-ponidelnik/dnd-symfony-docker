<?php

namespace App\Repository;

use App\Entity\Spell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Spell|null find($id, $lockMode = null, $lockVersion = null)
 * @method Spell|null findOneBy(array $criteria, array $orderBy = null)
 * @method Spell[]    findAll()
 * @method Spell[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpellRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Spell::class);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByIdentifier(string $id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.identifier = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /*
    public function findOneBySomeField($value): ?Spell
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
