<?php

namespace Satpro\OAuthServer\Entities;

use Satpro\OAuthServer\Entities\Traits\ClaimsAccessTokenTrait;
use Laravel\Passport\Bridge\AccessToken;
class TokenAccess extends AccessToken
{
    use ClaimsAccessTokenTrait;

}