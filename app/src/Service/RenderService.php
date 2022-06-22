<?php


namespace App\Service;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class RenderService
{

    const FOLDER_TEMPLATES = 'templates';

    const FOLDER_CACHE = 'twig';

    private string $path;

    private string $cache;

    private Environment $twig;

    private TemplateWrapper $template;

    public function __construct(string $path)
    {
        $this->path = sprintf('%s/%s', $path, self::FOLDER_TEMPLATES);
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }

        $this->cache = sprintf('%s/%s/%s', $path, PagesService::FOLDER_CACHE, self::FOLDER_CACHE);

        if (!is_dir(sprintf('%s/%s/%s', $path, PagesService::FOLDER_CACHE, self::FOLDER_CACHE))) {
            mkdir($this->cache);
        }


        $loader = new FilesystemLoader($this->path);

        $this->twig = new Environment($loader, [
            //'cache' => $this->cache
        ]);

    }

    public function load(array $page, array $menu)
    {
        $this->template = $this->twig->load($page['template']);
    }

    public function render($menu, $content, $page): string
    {
        return $this->template->render(['content' => $content, 'page' => $page, 'menu' => $menu]);
    }


    public function error(array $menu, $content = 'Error', $title = 'Main Error'): string
    {
        if (is_file(sprintf('%s/%s', $this->path, 'page500.html.twig'))) {
            $template = $this->twig->load(sprintf('%s/%s', $this->path, 'page500.html.twig'));
        } else {
            $template = $this->twig->createTemplate($this->template500());
        }

        return $template->render(['content' => [$content], 'page' => ['title' => $title], 'menu' => $menu]);
    }


    private function template500(): string
    {

        return "{% block body %}
                    <h1>Error 500</h1>
                    <p>{{ content.error }}</p>
                {% endblock %}
                ";

    }

}