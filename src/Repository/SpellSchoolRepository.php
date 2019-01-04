<?php

namespace App\Repository;

use App\Entity\SpellSchool;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpellSchool|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpellSchool|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpellSchool[]    findAll()
 * @method SpellSchool[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpellSchoolRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpellSchool::class);
    }
    /**
     * @param string $id
     * @return SpellSchool|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByIdentifier(string $id): ?SpellSchool
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.identifier = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * @param string $name
     * @return SpellSchool|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByRuName(string $name): ?SpellSchool
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nameRu = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
    // /**
    //  * @return SpellSchool[] Returns an array of SpellSchool objects
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
    public function findOneBySomeField($value): ?SpellSchool
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
