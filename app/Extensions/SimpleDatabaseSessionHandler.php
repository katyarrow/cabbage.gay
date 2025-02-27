<?php

namespace App\Extensions;

use Illuminate\Session\DatabaseSessionHandler;

class SimpleDatabaseSessionHandler extends DatabaseSessionHandler
{
    /**
     * Add the request information to the session payload.
     *
     * @param  array  $payload
     * @return $this
     */
    protected function addRequestInformation(&$payload)
    {
        return $this;
    }
}