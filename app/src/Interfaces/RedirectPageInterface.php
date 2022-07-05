<?php

namespace App\Interfaces;

interface RedirectPageInterface
{
    public function check(array $param): bool;
}