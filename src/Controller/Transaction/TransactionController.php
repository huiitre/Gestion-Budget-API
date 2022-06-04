<?php

namespace App\Controller\Transaction;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function transactionsList(TransactionRepository $tr): Response
    {
        return $this->json(
            $tr->findAll(),
            200,
            [],
            ['groups' => [
                'get_transactions'
                ]
            ]
        );
    }
}
