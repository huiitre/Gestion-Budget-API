<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationFailureListener
{
    /**
     * Message d'erreur en cas de fausses informations à l'authentification
     * @param AuthenticationFailureEvent  $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse('Saisie incorrect ! Vérifiez votre identifiant et mot de passe.', JsonResponse::HTTP_UNAUTHORIZED);
        $event->setResponse($response);
    }
}
