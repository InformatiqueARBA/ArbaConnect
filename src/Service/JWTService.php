<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;

/*
Service permettant la création d'un token de sécurité avec les contraintes associées
*/

class JWTService
{
    private $config;

    // Génère les méthodes des contraintes & du hash password
    public function __construct(string $jwtSecret)
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($jwtSecret)
        );

        $this->config->setValidationConstraints(
            new IssuedBy('ArbaConnect'),
            new LooseValidAt(SystemClock::fromUTC())
        );
    }

    // Création du token associé à l'envoi du mail sécurisé
    public function generateToken(array $claims): string
    {
        $now = new \DateTimeImmutable();
        $token = $this->config->builder()
            // Élements de validation injectés dans le token
            ->issuedBy('ArbaConnect')
            ->issuedAt($now)
            ->expiresAt($now->modify('+5 minute'))
            ->withClaim('data', $claims)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }


    // Test & valide le token utilisé pour le changement de password
    public function validateToken(string $token): array
    {
        $token = $this->config->parser()->parse($token);

        assert($token instanceof \Lcobucci\JWT\UnencryptedToken);

        if (!$this->config->validator()->validate($token, ...$this->config->validationConstraints())) {
            throw new \Exception('Token non valide');
        }

        return $token->claims()->all();
    }
}
