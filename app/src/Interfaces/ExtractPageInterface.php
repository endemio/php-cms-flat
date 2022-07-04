<?php


namespace App\Interfaces;


abstract class ExtractPageInterface
{
    abstract public function check(array $param):array;
}