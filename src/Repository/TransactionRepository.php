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

    public function transactionsList($user, $obj)
    {
        $month = date('m');
        $year = '20' . date('y');

        //* gestion des mois
        if (!empty($obj->month)) {
            $month = intval($obj->month);
        }

        //* gestion des années
        if (!empty($obj->year)) {
            $year = intval($obj->year);
        }

        //* gestion du tri par colonne
        if (!empty($obj->orderBy) && !empty($obj->order)) {
            $orderBy = "ORDER BY $obj->orderBy $obj->order";
        } else {
            $orderBy = "ORDER BY t.created_at desc";
        }

        //* gestion du limit/offset
        if (!empty($obj->limit) && isset($obj->offset)) {
            $limit = 'LIMIT ' . intval($obj->limit) . ' OFFSET ' . intval($obj->offset);
        } else {
            $limit = '';
        }

        $sql = "SELECT
                    t.id as t_id,
                    t.name as t_name,
                    t.wording as t_wording,
                    ROUND(t.balance, 2) as t_balance,
                    t.created_at as t_created_at,
                    t.slug as t_slug,
                    t.status as t_status,
                    s.id as s_id,
                    s.name as s_name,
                    c.id as c_id,
                    c.name as c_name
                from (
                    select *
                    from `transaction` t
                    where user_id = :user
                    and status = 1
                    and month(created_at) = :month
                    and year(created_at) = :year
                    union
                    select *
                    from `transaction` t
                    where user_id = :user
                    and status = 2
                    and month(created_at) = :month
                    and year(created_at) = :year
                ) t
                INNER JOIN `subcategory` s
                ON t.subcategory_id = s.id
                INNER JOIN `category` c
                ON s.category_id = c.id
                $orderBy
                $limit
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('month', $month);
        $query->bindValue('year', $year);
        $query->bindValue('user', $user->getId());
        
        return $query->execute()->fetchAllAssociative();
    }

    public function transactionFuelList($user, $obj)
    {
        $month = date('m');
        $year = '20' . date('y');

        //* gestion des mois
        if (!empty($obj->month)) {
            $month = intval($obj->month);
        }

        //* gestion des années
        if (!empty($obj->year)) {
            $year = intval($obj->year);
        }

        //* gestion du tri par colonne
        if (!empty($obj->orderBy) && !empty($obj->order)) {
            $orderBy = "ORDER BY $obj->orderBy $obj->order";
        } else {
            $orderBy = "ORDER BY t.created_at desc";
        }

        //* gestion du limit/offset
        if (!empty($obj->limit) && isset($obj->offset)) {
            $limit = 'LIMIT ' . intval($obj->limit) . ' OFFSET ' . intval($obj->offset);
        } else {
            $limit = '';
        }
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
    public function balance($user, $month, $year)
    {
        $sql = "SELECT ROUND(SUM(t.balance), 2) as total_balance, count(t.id) as total_count
                from (
                    select *
                    from `transaction` t 
                    where month(created_at) = :month
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 1
                    and year(created_at) = :year
                    union
                    select *
                    from `transaction` t
                    where month(created_at) = :month
                    and is_active = true 
                    and is_seen = true
                    and user_id = :user
                    and status = 2
                    and year(created_at) = :year
                ) t
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('month', $month);
        $query->bindValue('year', $year);
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
            $month = "and MONTH(t.created_at) = $month";
        }

        $sql = "SELECT
                    *
                FROM
                    (
                        SELECT
                            t.id as t_id, 
                            t.name as t_name,
                            t.wording as t_wording,
                            t.balance as t_balance,
                            t.created_at as t_created_at,
                            t.slug as t_slug,
                            t.status as t_status,
                            s.id as s_id,
                            s.name as s_name,
                            c.id as c_id,
                            c.name as c_name
                        FROM
                            `transaction` t
                        INNER JOIN `subcategory` s
                        ON t.subcategory_id = s.id
                        INNER JOIN `category` c
                        ON s.category_id = c.id
                        WHERE
                            t.user_id = :user
                            AND t.status = 1
                            AND 
                            CASE
                                WHEN :year is null
                                THEN year(t.created_at) = year(CURRENT_TIMESTAMP)
                                ELSE YEAR(t.created_at) = :year
                            END
                            $month
                        UNION
                        SELECT
                            t.id as t_id, 
                            t.name as t_name,
                            t.wording as t_wording,
                            t.balance as t_balance,
                            t.created_at as t_created_at,
                            t.slug as t_slug,
                            t.status as t_status,
                            s.id as s_id,
                            s.name as s_name,
                            c.id as c_id,
                            c.name as c_name
                        FROM
                            `transaction` t
                        INNER JOIN `subcategory` s
                        ON t.subcategory_id = s.id
                        INNER JOIN `category` c
                        ON s.category_id = c.id
                        WHERE
                            t.user_id = :user
                            AND t.status = 2
                            AND 
                            CASE
                                WHEN :year is null
                                THEN year(t.created_at) = year(CURRENT_TIMESTAMP)
                                ELSE YEAR(t.created_at) = :year
                            END
                            $month
                    ) t
                ORDER BY
                    t.t_created_at
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
        // if ($year == null) $year = '20' . date('y');

        if ($month !== null) {
            $month = "and MONTH(created_at) = $month";
        }

        $sql = "SELECT
                    ROUND(SUM(t.balance), 2) as balance, count(t.id) as count
                FROM
                    (
                        SELECT
                            *
                        FROM
                            `transaction` t
                        WHERE
                            is_active = true
                            AND is_seen = true
                            AND user_id = :user
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
                            AND user_id = :user
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

    public function deleteTransaction($user, $array)
    {
        $ids = $array;

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->delete('App\Entity\Transaction', 't')
            ->where('t.id IN (:ids)')
            ->andWhere('t.user = :user')
            ->setparameter('ids', $ids)
            ->setParameter('user', $user->getId());

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
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
