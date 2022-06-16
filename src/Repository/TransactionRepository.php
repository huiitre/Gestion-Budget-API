<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function transactionsByMonth($user,  $month = null, $year = null)
    {
        if ($month == null) $month = date('m');
        if ($year == null) $year = '20' . date('y');

        if ($year !== null && $month !== null) {
            $year = "and YEAR(created_at) = $year";
        }

        $sql = "select *
                from (
                    select *
                    from `transaction` t 
                    where month(created_at) = :month
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 1
                    $year
                    union
                    select *
                    from `transaction` t
                    where month(created_at) = :month
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 2
                    $year
                ) t ORDER BY t.created_at
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('month', $month);
        $query->bindValue('user', $user->getId());

        return $query->execute()->fetchAllAssociative();
    }

    /**
     ** Permet de récupérer la somme sur un mois en particulier de l'année en cours, ou en ajoutant une année en particulier
     * 
     * Il doit toujours y avoir un mois de renseigné, sinon cela prendra le mois en cours
     *
     * @param [type] $user
     * @param [type] $month
     * @param [type] $year
     * @return void
     */
    public function balanceByMonth($user, $month = null, $year = null)
    {
        if ($month == null) $month = date('m');
        if ($year == null) $year = '20' . date('y');

        if ($year !== null && $month !== null) {
            $year = "and YEAR(created_at) = $year";
        }

        $sql = "select ROUND(SUM(t.balance), 2) as balance, count(t.id) as count
                from (
                    select *
                    from `transaction` t 
                    where month(created_at) = :month
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 1
                    $year
                    union
                    select *
                    from `transaction` t
                    where month(created_at) = :month
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 2
                    $year
                ) t
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('month', $month);
        $query->bindValue('user', $user->getId());

        return $query->execute()->fetchAllAssociative();
    }

    /**
     ** Permet de récupérer la liste des transactions sur l'année en cours, ou une année passée en paramètre, avec éventuellement un mois 
     *
     * @param [type] $user
     * @param [type] $year
     * @param [type] $month
     * @return void
     */
    public function transactionsByYear($user, $year = null, $month = null)
    {
        // if ($year == null) $year = '20' . date('y');

        if ($month !== null) {
            $month = "and MONTH(created_at) = $month";
        }

        $sql = "SELECT
                    *
                FROM
                    (
                        SELECT
                            *
                        FROM
                            `transaction` t
                        WHERE
                            is_active = true
                            AND is_seen = true
                            AND user_id = 1
                            AND status = 1
                            AND 
                            CASE
                                WHEN :year is null
                                THEN year(created_at) = year(CURRENT_TIMESTAMP)
                                ELSE YEAR(created_at) = :year
                            END
                            $month
                        UNION
                        SELECT
                            *
                        FROM
                            `transaction` t
                        WHERE
                            is_active = true
                            AND is_seen = true
                            AND user_id = 1
                            AND status = 2
                            AND 
                            CASE
                                WHEN :year is null
                                THEN year(created_at) = year(CURRENT_TIMESTAMP)
                                ELSE YEAR(created_at) = :year
                            END
                            $month
                    ) t
                ORDER BY
                    t.created_at desc;
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('year', $year);
        $query->bindValue('user', $user->getId());

        return $query->execute()->fetchAllAssociative();
    }

    /**
     ** Permet de récupérer la somme sur une année complète, ou une année avec un mois en particulier
     * 
     * Il doit toujours y avoir une année de renseignée, sinon cela prendra l'année en cours
     *
     * @param [type] $user
     * @param [type] $year
     * @param [type] $month
     * @return void
     */
    public function balanceByYear($user, $year = null, $month = null)
    {
        if ($year == null) $year = '20' . date('y');

        if ($month !== null) {
            $month = "and MONTH(created_at) = $month";
        }

        $sql = "select ROUND(SUM(t.balance), 2) as balance, count(t.id) as count
                from (
                    select *
                    from `transaction` t 
                    where year(created_at) = :year
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 1
                    $month
                    union
                    select *
                    from `transaction` t
                    where year(created_at) = :year
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 2
                    $month
                ) t
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('year', $year);
        $query->bindValue('user', $user->getId());

        return $query->execute()->fetchAllAssociative();
    }



    //    /**
    //     * @return Transaction[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
