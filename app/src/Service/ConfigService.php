<?php


namespace App\Service;


class ConfigService extends DefaultService
{

    protected string $path;

    public function __construct(string $website_path)
    {
        $this->website_path = $website_path;
        $this->path = $this->checkFolderExist(sprintf('%s/%s', $website_path, self::FOLDER_CONFIG));
    }

    public function loadConfig($filename): array
    {

        return $this->load(sprintf('%s/%s', $this->path, $filename));

    }

}