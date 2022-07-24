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
        $sql = "CALL createTodo(:name, :user, :created_at, :is_done, :percent, :list_id)
        ";
        $conn = $this->getEntityManager()->getConnection();

        $query = $conn->prepare($sql);
        $query->bindValue('name', $todo->getName(), PDO::PARAM_STR);
        $query->bindValue('user', $user->getId(), PDO::PARAM_INT);
        $query->bindValue('created_at', $todo->getCreatedAt());
        $query->bindValue('is_done', $todo->isIsDone(), PDO::PARAM_BOOL);
        $query->bindValue('list_id', $todo->getTodolist()->getId(), PDO::PARAM_INT);
        $query->bindValue('percent', $todo->getPercent(), PDO::PARAM_INT);

        return $query->executeStatement();
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
