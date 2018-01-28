<?php

namespace App\OAuth;

interface ExpirableTokensInterface
{
    /**
     * Clears all expired tokens.
     *
     * @return void
     */
    public function clearExpiredTokens();
}
