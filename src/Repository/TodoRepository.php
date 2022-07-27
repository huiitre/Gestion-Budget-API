<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @extends ServiceEntityRepository<Todo>
 *
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todo::class);
    }

    public function add(Todo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Todo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function showTodos($user, $id)
    {
        $sql = "SELECT t.*
                from todo t 
                where todolist_id in (
                    select t2.id 
                    from todolist t2 
                    where t2.user_id = :user
                )
                and todolist_id = :list
        ";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('list', $id);
        $query->bindValue('user', $user->getId());

        return $query->executeQuery()->fetchAllAssociative();
    }

    public function createTodo($user, $todo)
    {
        $ok = 1;

        //* on check si l'user est le bon
        $ok = $this->checkUserTodolist($todo, $user);

        if ($ok) {
            $conn = $this->getEntityManager()->getConnection();
            //* requête d'insertion todo
            $sql = "
                CALL createTodo(:name, :user, :created_at, :is_done, :percent, :list_id)
            ";
            //* insertion todo
            $qry = $conn->prepare($sql);
            $qry->bindValue('name', $todo->getName(), PDO::PARAM_STR);
            $qry->bindValue('user', $user->getId(), PDO::PARAM_INT);
            $qry->bindValue('created_at', $todo->getCreatedAt());
            $qry->bindValue('is_done', $todo->isIsDone(), PDO::PARAM_BOOL);
            $qry->bindValue('list_id', $todo->getTodolist()->getId(), PDO::PARAM_INT);
            $qry->bindValue('percent', $todo->getPercent(), PDO::PARAM_INT);
            $ok = $qry->executeStatement();
        }

        //* on recalcul la liste
        $this->calculTodolistByTodo($todo->getTodolist()->getId());

        return $ok;
    }

    public function deleteTodo($ids, $user, $list)
    {
        $countArray = count($ids);

        $conn = $this->getEntityManager()->getConnection();
        $conn->beginTransaction();

        $count = 0;
        foreach ($ids as $value) {
            $sql = "CALL deleteTodo(:list, :user, :todo)";
            $query = $conn->prepare($sql);
            $query->bindValue('list', $list, PDO::PARAM_INT);
            $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
            $query->bindValue('todo', $value, PDO::PARAM_INT);

            if ($query->executeStatement() == 1)
                $count++;
        }

        if ($countArray == $count) {
            $conn->commit();
            $sql = "CALL calculTodolistByTodo(:list_id)";
            $query = $conn->prepare($sql);
            $query->bindValue('list_id', $list, PDO::PARAM_INT);
            $query->executeStatement();
            return $count;
        } else {
            $conn->rollback();
            return 0;
        }
    }

    public function updateTodo($user, $todo)
    {
        //* On récupère l'id de la liste
        $list_id = $this->getTodolistByTodo($todo->id);

        //* on update
        $sql = "CALL updateTodo(:todo_id, :name, :percent, :user)";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('todo_id', $todo->id, PDO::PARAM_INT);
        $query->bindValue('name', $todo->name, PDO::PARAM_STR);
        $query->bindValue('percent', $todo->percent, PDO::PARAM_INT);
        $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
        $ok = $query->executeStatement();

        //* on recalcul la liste APRES L EXECUTION DE LA PRECEDENTE REQUETE
        $this->calculTodolistByTodo($list_id);

        return $ok;
    }

    public function checkUserTodolist($todo, $user)
    {
        $conn = $this->getEntityManager()->getConnection();
        //todo Vérification de la liste et de l'utilisateur
        $sql = "CALL checkUserTodolist(:list_id, :user)";
        $qry = $conn->prepare($sql);
        $qry->bindValue('list_id', $todo->getTodolist()->getId(), PDO::PARAM_INT);
        $qry->bindValue('user', $user->getId(), PDO::PARAM_INT);

        return $qry->executeStatement();
    }

    public function calculTodolistByTodo($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        //* requête de recalcul de la todolist
        $sql = "CALL calculTodolistByTodo(:list_id)";
        //* recalcul todolist
        $qry = $conn->prepare($sql);
        $qry->bindValue('list_id', $id, PDO::PARAM_INT);
        return $qry->executeStatement();
    }

    public function getTodolistByTodo($id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "CALL getTodolistByTodo(:id)";
        $qry = $conn->prepare($sql);
        $qry->bindValue('id', $id, PDO::PARAM_INT);
        return $qry->executeQuery()->fetchOne();
    }

//    /**
//     * @return Todo[] Returns an array of Todo objects
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

//    public function findOneBySomeField($value): ?Todo
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
