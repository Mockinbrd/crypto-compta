<?php

namespace App\Controller;

use App\Client\CoinGeckoClient;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PurchaseController
 * @package App\Controller
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/purchase/new", name="purchase_new")
     */
    public function new(CoinGeckoClient $coinGeckoClient): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('isVerifiedCheck',$user);

        $coins = array_slice($coinGeckoClient->list(),0,25);

        return $this->render('purchase/new.html.twig', [
            'coins' => $coins,
        ]);
    }
}