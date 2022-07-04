<?php

namespace App\Service;

use App\Exceptions\PageNotFound;

class PagesService extends DefaultService
{

    private string $pages;

    public function __construct(string $path)
    {
        $this->pages = $this->checkFolderExist(sprintf('%s/%s', $path, self::CONTENT_FOLDER));
    }

    /**
     * @param string $full_path
     * @return array
     * @throws PageNotFound
     */
    public function data(string $full_path): array
    {
        $url = parse_url($full_path);

        $page =  parent::load($this->pages . $url['path'] . '/index.yaml');

        if (empty($page)){
            throw new PageNotFound(sprintf('Config for %s not found',$full_path));
        }

        $content = [];

        if (!empty($page['content'])) {
            foreach ($page['content'] as $content_file) {
                $filename = sprintf('%s/%s/%s.html', $this->pages, $url['path'], $content_file);

                try {
                    if (is_file($filename)) {
                        array_push($content, file_get_contents($filename));
                    }
                } catch (\Exception $exception) {

                }
            }
        }

        return [$page, $content];

    }
}