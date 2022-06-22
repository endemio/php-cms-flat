<?php


namespace App\Service;


use Symfony\Component\Yaml\Yaml;

class ConfigService
{

    const FOLDER_CONFIG = 'config';

    private string $path;

    public function __construct(string $path)
    {
        $this->path = sprintf('%s/%s',$path,self::FOLDER_CONFIG);
        if (!is_dir($this->path)){
            mkdir($this->path);
        }
    }

    public function load(){

        $this->config = Yaml::parseFile(sprintf('%s/%s', $this->path, 'config.yaml'));

    }


}