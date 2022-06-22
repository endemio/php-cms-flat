<?php

namespace App\Controller;

use App\Exceptions\PageNotFound;
use Symfony\Component\Yaml\Yaml;

class MainController
{

    const CONTENT_FOLDER = 'pages';

    private \Parsedown $parse;

    /**
     * @param string $path
     * @return array
     * @throws PageNotFound
     */
    public function parseMD(string $path): array
    {

        $this->parse = new \Parsedown();

        if (!is_file($path)) {
            throw new PageNotFound('File not found ->' . $path);
        }

        $content = file_get_contents($path);

        $parts = explode('-----', $content);

        foreach ($parts as $index => $part) {
            if ($index < 2) {
                continue;
            }

            $parts[$index] = $this->parse->text($part);

        }

        $page_config = Yaml::parse($parts[1]);

        return [$page_config, array_splice($parts, 2)];

    }

}