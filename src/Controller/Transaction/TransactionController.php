<?php

namespace App\Controller\Transaction;

use App\Entity\Month;
use App\Entity\Subcategory;
use App\Entity\Transaction;
use App\Entity\User;
use App\Models\JsonError;
use App\Repository\TransactionRepository;
use App\Service\MySlugger;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    /**
     * @Route("/list", name="list")
     */
    public function showTransactionsList(TransactionRepository $tr): Response
    {
        $user = $this->getUser();
        return $this->json(
            [
                'balance' => 1000,
                'data' => $tr->findBy(['user' => $user]),
            ],
            200,
            [],
            [
                'groups' => [
                    'get_transactions'
                ]
            ]
        );
    }

    /**
     * @Route("/list/month/{month?}/{year?}", name="list_by_month")
     *
     * @param TransactionRepository $tr
     * @param [type] $month
     * @param [type] $year
     * @return Response
     */
    public function showTransactionsByMonth(TransactionRepository $tr, $month, $year): Response
    {
        $user = $this->getUser();
        $data = $tr->transactionsByMonth($user, $month, $year);
        return $this->json(
            $data,
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
    public function showBalanceByMonth(TransactionRepository $tr, $month, $year): Response
    {
        $user = $this->getUser();
        $data = $tr->balanceByMonth($user, $month, $year);
        return $this->json(
            $data,
            200,
            []
        );
    }

    /**
     * @Route("/list/year/{year?}/{month?}", name="list_by_year")
     *
     * @param TransactionRepository $tr
     * @param [type] $year
     * @param [type] $month
     * @return Response
     */
    public function showTransactionsByYear(TransactionRepository $tr, $year, $month):Response
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
    public function showBalanceByYear(TransactionRepository $tr, $year, $month): Response
    {
        $user = $this->getUser();
        $data = $tr->balanceByYear($user, $year, $month);
        return $this->json(
            $data,
            200,
            []
        );
    }

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
    ): Response
    {
        $data = $req->getContent();
        $user = $this->getUser();

        try {
            $newTransaction = $serializer->deserialize($data, Transaction::class, 'json');
        } catch (Exception $e) {
            return new JsonResponse('JSON invalide', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $errors = $validator->validate($newTransaction);
        // dd(count($errors));
        if (count($errors) > 0) {
            $myJsonError = new JsonError(Response::HTTP_UNPROCESSABLE_ENTITY, 'Des erreurs de validation ont été trouvées');
            $myJsonError->setValidationErrors($errors);
            // return new JsonResponse($myJsonError, Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this->json($myJsonError, $myJsonError->getError());
        }

        dd($newTransaction);

        $slug = $slugger->slug($newTransaction->getName());
        $newTransaction->setSlug($slug);

        $newTransaction->setDebitedAt(new DateTimeImmutable('now'));
        $newTransaction->setCreatedAt(new DateTimeImmutable('now'));
        $newTransaction->setUser($user);


        $em->persist($newTransaction);

        $em->flush();

        return $this->json(
            $newTransaction,
            response::HTTP_CREATED,
            [],
            ['groups' => 'get_transactions']
        );
    }
}
