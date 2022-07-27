<?php

namespace App\Repository;

use App\Entity\Todolist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @extends ServiceEntityRepository<Todolist>
 *
 * @method Todolist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todolist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todolist[]    findAll()
 * @method Todolist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodolistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todolist::class);
    }

    public function add(Todolist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Todolist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère les todolist de l'utilisateur
     *
     * @param [type] $user
     * @return void
     */
    public function showTodolist($user)
    {
        $sql = "SELECT t.*,
                    c.name as category
                from todolist t
                inner join category c 
                on t.category_id = c.id
                where user_id = 1
                order by t.created_at desc
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('user', $user->getId());

        return $query->executeQuery()->fetchAllAssociative();
    }

    public function createTodolist($list, $user)
    {
        $sql = "CALL createTodolist(:name, :category, :user, :is_done, :percent, :created_at, :all_todos, :active_todos, :done_todos)";

        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('name', $list->getName(), PDO::PARAM_STR);
        $query->bindValue('category', $list->getCategory()->getId(), PDO::PARAM_INT);
        $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
        $query->bindValue('is_done', $list->isIsDone(), PDO::PARAM_BOOL);
        $query->bindValue('percent', $list->getPercent(), PDO::PARAM_INT);
        $query->bindValue('created_at', $list->getCreatedAt());
        $query->bindValue('all_todos', $list->getAllTodos(), PDO::PARAM_INT);
        $query->bindValue('active_todos', $list->getActiveTodos(), PDO::PARAM_INT);
        $query->bindValue('done_todos', $list->getDoneTodos(), PDO::PARAM_INT);

        return $query->executeStatement();
    }

    public function deleteTodolist($ids, $user)
    {
        $countArray = count($ids);

        $conn = $this->getEntityManager()->getConnection();
        $conn->beginTransaction();

        $count = 0;
        foreach ($ids as $value) {
            $sql = "CALL deleteTodolist(:id, :user)";
            $query = $conn->prepare($sql);
            $query->bindValue('id', $value, PDO::PARAM_INT);
            $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
            
            if ($query->executeStatement() == 1)
                $count++;
        }

        if ($countArray == $count) {
            $conn->commit();
            return $count;
        } else {
            $conn->rollback();
            return 0;
        }
    }

    public function updateTodolist($user, $list)
    {
        $sql = "CALL updateTodolist(:id, :user, :name, :category)";

        $conn = $this->getEntityManager()->getConnection();

        $query = $conn->prepare($sql);
        $query->bindValue('id', $list->id, PDO::PARAM_INT);
        $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
        $query->bindValue('name', $list->name, PDO::PARAM_STR);
        $query->bindValue('category', $list->category, PDO::PARAM_INT);

        return $query->executeStatement();
    }

//    /**
//     * @return Todolist[] Returns an array of Todolist objects
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

//    public function findOneBySomeField($value): ?Todolist
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
