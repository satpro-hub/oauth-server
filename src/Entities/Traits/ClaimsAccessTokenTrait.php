<?php

namespace Satpro\OAuthServer\Entities\Traits;

use Satpro\OAuthServer\Repositories\UserRepository;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

trait ClaimsAccessTokenTrait
{
    use AccessTokenTrait;

    public static string $userModel = 'App\Models\User';

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return $this->convertToJWT()->toString();
    }


    private function customConvertToJWT(Builder $builder): Builder
    {
        $user = $this->getUser();
        return $builder
            ->withClaim('login', $user->login ?? null)
            ->withClaim('lastname', $user->lastname ?? null)
            ->withClaim('firstname', $user->firstname ?? null)
            ->withClaim('patronymic', $user->patronymic ?? null);
    }

    /**
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT(): Token
    {
        $this->initJwtConfiguration();

        $builder = $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new \DateTimeImmutable())
            ->canOnlyBeUsedAfter(new \DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string)$this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes());
        $builder = $this->customConvertToJWT($builder);
        return $builder->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    private function getUser()
    {
        return self::user()::find($this->getUserIdentifier());
    }

    /**
     * @return string
     */
    public static function user(): string
    {
        return static::$userModel;
    }

    /**
     * Get the client model class name.
     *
     * @return string
     */
    public static function clientModel()
    {
        return static::$userModel;
    }


}