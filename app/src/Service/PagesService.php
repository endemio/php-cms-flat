<?php

namespace App\Service;

use App\Controller\MainController;

class PagesService extends MainController
{

    private string $folder;

    private string $pages;

    const FOLDER_CACHE = 'cache';


    public function __construct(string $path)
    {
        $this->folder = $path;

        $this->pages = sprintf('%s/'.self::CONTENT_FOLDER, $path);

        if (!is_dir($this->pages)) {
            mkdir($this->pages);
        }

        $cache = sprintf('%s/%s', $path, self::FOLDER_CACHE);

        if (!is_dir($cache)) {
            mkdir($cache);
        }

    }

    /**
     * @param string $full_path
     * @return array
     * @throws \Exception
     */
    public function data(string $full_path): array
    {

        $url = parse_url($full_path);

        list($page_config, $contents) = parent::parseMD($this->pages . $url['path'] . '/index.md');

        return [$page_config, $contents];
    }

    /**
     * @return string
     */
    public function Folder(): string
    {
        return $this->folder;
    }

}