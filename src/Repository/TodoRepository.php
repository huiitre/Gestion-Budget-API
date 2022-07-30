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
        $sql = "SELECT
                    t.*, c.name as category
                from
                    todo t
                left outer join todolist t3
                on t3.id = t.todolist_id 
                left outer join category c 
                on c.id = t3.category_id 
                where
                    todolist_id in (
                        select
                            t2.id
                        from
                            todolist t2
                        where
                            t2.user_id = :user
                    )
                and todolist_id = :list
                order by t.created_at desc
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
        $ok = $this->checkUserTodolist($todo->getTodolist()->getId(), $user->getId());

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
        if (!$list_id)
            return 0;

        //* on vérifie que l'user a les droits
        $ok = $this->checkUserTodolist(intval($list_id), $user->getId());
        if (!$ok)
            return 0;

        //* on update
        $sql = "CALL updateTodo(:todo_id, :name, :percent, :user)";
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->prepare($sql);
        $query->bindValue('todo_id', $todo->id, PDO::PARAM_INT);
        $query->bindValue('name', $todo->name, PDO::PARAM_STR);
        $query->bindValue('percent', $todo->percent, PDO::PARAM_INT);
        $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
        $query->executeStatement();

        //* on recalcul la liste APRES L EXECUTION DE LA PRECEDENTE REQUETE
        $this->calculTodolistByTodo($list_id);

        return 1;
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
