<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Entity\User;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created')]
class JWTCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();

        if ($user instanceof \App\Entity\User) {
            // Vérifier si c'est un producteur
            if (in_array('ROLE_PRODUCTEUR', $user->getRoles())) {
                // Bloquer si pas approuvé
                if ($user->getStatus() !== User::STATUS_APPROVED) {
                    $message = match ($user->getStatus()) {
                        User::STATUS_PENDING => 'Votre compte est en attente de validation par un administrateur.',
                        User::STATUS_REJECTED => 'Votre compte a été rejeté.',
                        default => 'Votre compte n\'est pas actif.'
                    };

                    throw new AccessDeniedHttpException($message);
                }
            }
            $payload['id'] = $user->getId();  //Ajoute l'ID dans le JWT
        }

        $event->setData($payload);
    }
}
