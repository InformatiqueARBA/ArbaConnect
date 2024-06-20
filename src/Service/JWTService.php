<?php

namespace App\Service;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;

class JWTService
{
    private $config;

    public function __construct(string $jwtSecret)
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($jwtSecret)
        );

        // Ajout des contraintes de validation
        $this->config->setValidationConstraints(
            new IssuedBy('ArbaConnect'),
            //new Validator(SystemClock::fromUTC())
        );
    }

    public function generateToken(array $claims): string
    {
        $now = new \DateTimeImmutable();
        $token = $this->config->builder()
            ->issuedBy('ArbaConnect')
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('data', $claims)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    public function validateToken(string $token): array
    {
        $token = $this->config->parser()->parse($token);
        //dd($token);
        assert($token instanceof \Lcobucci\JWT\UnencryptedToken);

        if (!$this->config->validator()->validate($token, ...$this->config->validationConstraints())) {
            throw new \Exception('Token non valide');
        }

        return $token->claims()->all();
    }
}
