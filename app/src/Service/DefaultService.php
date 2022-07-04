<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class DefaultService
{
    const FOLDER_CACHE = 'cache';
    const FOLDER_CONFIG = 'config';
    const CONTENT_FOLDER = 'pages';
    const FOLDER_TEMPLATES = 'templates';

    protected string $website_path;

    public function checkFolderExist($path, $is_create = true)
    {

        if (!is_dir($path) && $is_create) {
            mkdir($path);
        }

        return $path;
    }

    public function getYaml($yaml_string)
    {
        return Yaml::parse($yaml_string);
    }

    public function load(string $path): array
    {
        if (is_file($path)) {
            return Yaml::parseFile($path);
        }

        return [];
    }


}