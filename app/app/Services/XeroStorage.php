<?php

namespace App\Services;

class XeroStorage
{
    protected $key = 'xero_oauth2';

    public function setToken(array $data)
    {
        session([$this->key => $data]);
    }

    public function get($key)
    {
        return session($this->key)[$key] ?? null;
    }

    public function hasExpired(): bool
    {
        $expires = $this->get('expires');
        return !$expires || time() > $expires;
    }
}
