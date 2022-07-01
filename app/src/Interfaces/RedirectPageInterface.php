<?php


namespace App\Interfaces;


abstract class RedirectPageInterface
{
    abstract public function check(array $param):array;
}