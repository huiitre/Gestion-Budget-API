<?php

namespace App\Controller\Transaction;

use App\Entity\Month;
use App\Entity\Subcategory;
use App\Entity\User;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->json(
            $tr->findAll(),
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
     * @Route("/user/{id}/subcategory/{$id2}", name="user_id")
     *
     * @param Request $request
     * @return Response
     */
    public function showTransactionsBySubcategory(Request $request, TransactionRepository $tr, User $user, Subcategory $subcategory): Response
    {
        $transactionsList = $tr->findBy(['subcategory' => $subcategory]);

        return $this->json(
            $transactionsList,
            200,
            [],
            [
                'groups' => [
                    'get_transactions'
                ]
            ]
        );
    }
}
