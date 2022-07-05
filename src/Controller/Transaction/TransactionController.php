<?php

namespace App\Controller\Transaction;

use App\Entity\Month;
use App\Entity\Subcategory;
use App\Entity\Transaction;
use App\Entity\User;
use App\Models\JsonError;
use App\Repository\TransactionRepository;
use App\Service\MySlugger;
use ContainerCHiAHmo\getSubcategoryControllerService;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/transaction", name="api_transaction_")
 */
class TransactionController extends AbstractController
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/list", name="list")
     * 
     *  {
     *      "orderBy": "t_name",
     *      "order": "asc",
     *      "limit": 5,
     *      "offset": 5,
     *      "month": 4,
     *      "year": 2022
     *  }
     * 
     * @param TransactionRepository $tr
     * @return Response
     */
    public function showTransactionsList(TransactionRepository $tr, Request $req): Response
    {
        $body = $req->getContent();
        if ($body !== "") {
            $obj = json_decode($body);
        } else {
            $obj = null;
        }

        $user = $this->getUser();

        $data = $tr->transactionsList(
            $user,
            $obj
        );

        $month = !empty($obj->month) ? $obj->month : date('m');
        $year = !empty($obj->year) ? $obj->year : '20' . date('y');
        $count = $tr->balance($user, $month, $year);

        //? créer un service (fonction) qui va gérer le next et previous url plus tard
        if (!empty($obj->limit) && isset($obj->offset)) {
            $reqUri = 'http://localhost:8080';
            $path = $req->getPathInfo();
            $previousOffset = ($obj->offset - $obj->limit) < 0 ? null : $reqUri . $path . '?limit='.$obj->limit.'&offset=' . ($obj->offset - $obj->limit);

            $nextOffset = $obj->offset > count($data) ? null : $reqUri . $path . '?limit='.$obj->limit.'&offset=' . ($obj->offset + $obj->limit);

            $return = [
                'total' => $count[0],
                'next' => $nextOffset,
                'previous' => $previousOffset,
                'data' => $data,
            ];
        } else {
            $return = [
                'total' => $count[0],
                'data' => $data,
            ];
        }

        
        return $this->json(
            $return,
            200,
            []
        );
    }

    /**
     * @Route("/balance/month/{month?}/{year?}", name="balance_by_month")
     *
     * @param TransactionRepository $tr
     * @return Response
     */
    /* public function showBalanceByMonth(TransactionRepository $tr, $month, $year): Response
    {
        $user = $this->getUser();
        $data = $tr->balanceByMonth($user, $month, $year);
        return $this->json(
            $data,
            200,
            []
        );
    } */

    /**
     * @Route("/list/year/{year?}/{month?}", name="list_by_year")
     *
     * @param TransactionRepository $tr
     * @param [type] $year
     * @param [type] $month
     * @return Response
     */
    public function showTransactionsByYear(TransactionRepository $tr, $year, $month): Response
    {
        $user = $this->getUser();
        $data = $tr->transactionsByYear($user, $year, $month);
        return $this->json(
            $data,
            200,
            []
        );
    }

    /**
     * @Route("/balance/year/{year?}/{month?}", name="balance_by_year")
     *
     * @param TransactionRepository $tr
     * @return Response
     */
    /* public function showBalanceByYear(TransactionRepository $tr, $year, $month): Response
    {
        $user = $this->getUser();
        $data = $tr->balanceByYear($user, $year, $month);
        return $this->json(
            $data,
            200,
            []
        );
    } */

    /**
     * @Route("/create", name="create", methods={"POST"})
     *
     * @return Response
     */
    public function createTransaction(
        Request $req,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ): Response {
        $data = $req->getContent();
        $user = $this->getUser();


        try {
            $newTransaction = $serializer->deserialize($data, Transaction::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse('JSON invalide : ' . $e, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newTransaction);
        // dd(count($errors));
        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, 'Des erreurs de validation ont été trouvées.');
            $myJsonError->setValidationErrors($errors);
            // dd($errors[0]);
            // return new JsonResponse($myJsonError, Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        $slug = $slugger->slug($newTransaction->getName());
        $newTransaction->setSlug($slug);

        $newTransaction->setCreatedAt(new DateTimeImmutable('now'));
        $newTransaction->setUser($user);

        if ($newTransaction->getBalance() > 0) {
            $newTransaction->setStatus(1);
        } else {
            $newTransaction->setStatus(2);
        }

        /* if ($newTransaction->getSubcategory()->getId() === 40) {
            dd($newTransaction);
        }

        dd($newTransaction->getSubcategory()->getId()); */


        $em->persist($newTransaction);

        try {
            //? on va envoyer les informations en base de données.
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse("Erreur", Response::HTTP_CONFLICT);
        }

        return $this->json(
            $newTransaction,
            response::HTTP_CREATED,
            [],
            ['groups' => 'get_transactions']
        );
    }

    /**
     * @Route("/delete", name="delete", methods={"DELETE"})
     *
     * @param TransactionRepository $tr
     * @param EntityManagerInterface $em
     * @param Request $req
     * @return void
     */
    public function deleteTransaction(TransactionRepository $tr, EntityManagerInterface $em, Request $req,SerializerInterface $serializer)
    {
        $user = $this->getUser();
        $data = $req->get('id');
        $array = json_decode($data);

        $result = $tr->deleteTransaction($user, $array);
        if ($result > 0) {
            return $this->json(
                [
                    'status_code' => 1,
                    'message' => 'La suppression a bien été effectué',
                ],
                Response::HTTP_OK,
                [],
            );
        } else {
            return $this->json(
                [
                    'status_code' => 2,
                    'message' => 'Veuillez sélectionner une transaction à supprimer',
                ],
                Response::HTTP_OK,
                [],
            );
        }

    }
}
