<?php


namespace App\Service\View;


use App\Service\DefaultService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class RenderService extends DefaultService
{

    private string $templates;

    private string $cache;

    private Environment $twig;

    private TemplateWrapper $template;

    public function __construct(string $path)
    {
        $this->templates = $this->checkFolderExist(sprintf('%s/%s', $path, self::FOLDER_TEMPLATES));

        $this->cache = $this->checkFolderExist(sprintf('%s/%s/%s', $path, self::FOLDER_CACHE, 'twig'));

        $loader = new FilesystemLoader($this->templates);

        $this->twig = new Environment($loader, [
            //'cache' => $this->cache
        ]);
    }

    public function loadTemplate(array $page, array $menu)
    {

        $this->template = $this->twig->load($page['template']);
    }

    public function render($menu, $content, $page): string
    {
        return $this->template->render(['content' => $content, 'page' => $page, 'menu' => $menu]);
    }

    public function page404(array $menu, $content = 'Page not found', $title = 'Page not found error'): string
    {
        $template404 = sprintf('%s/%s', $this->templates, 'page404.html.twig');

        if (is_file($template404)) {
            $template = $this->twig->load('page404.html.twig');
        } else {
            $template = $this->twig->createTemplate($this->template500());
        }

        return $template->render(['content' => [$content], 'page' => ['title' => $title], 'menu' => $menu]);
    }

    public function error(array $menu, $content = 'Error', $title = 'Main Error'): string
    {
        $template500 = sprintf('%s/%s', $this->templates, 'page500.html.twig');

        if (is_file($template500)) {
            $template = $this->twig->load('page500.html.twig');
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