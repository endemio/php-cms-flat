<?php


namespace App\Service;


use Symfony\Component\Yaml\Yaml;

class ConfigService
{

    const FOLDER_CONFIG = 'config';

    private string $path;

    protected string $website_path;

    public function __construct(string $website_path)
    {
        $this->website_path = $website_path;
        $this->path = sprintf('%s/%s', $website_path, self::FOLDER_CONFIG);
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
    }

    public function load(string $file = 'config.yaml'): array
    {
        if (is_file(sprintf('%s/%s', $this->path, $file))) {
            return Yaml::parseFile(sprintf('%s/%s', $this->path, $file));
        }
        return [];
    }


}