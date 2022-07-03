<?php

namespace App;

use App\Exceptions\PageNotFound;
use App\Service\ConfigService;
use App\Service\MenuService;
use App\Service\PagesService;
use App\Service\TransformService;
use App\Service\RenderService;


class Kernel
{

    const FOLDER_SITES = 'sites';

    private string $website_folder;

    private PagesService $page_service;

    private MenuService $menu_service;

    private ConfigService $config_service;

    private RenderService $render_service;

    private TransformService $redirect_service;

    public function __construct(string $website)
    {
        $this->website_folder = sprintf('%s/%s/%s',$this->getProjectDir() , self::FOLDER_SITES, $website);

        $this->page_service = new PagesService($this->website_folder);
        $this->menu_service = new MenuService($this->website_folder);
        $this->config_service = new ConfigService($this->website_folder);
        $this->render_service = new RenderService($this->website_folder);
        $this->redirect_service = new TransformService($this->website_folder);

    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function run(): void
    {

        $menu = [
            'top' => $this->menu_service->fetchMenuTopLevel(),
            'current' => $this->menu_service->fetchMenuCurrentLevel($_SERVER['REQUEST_URI']),
            'children'=>$this->menu_service->fetchMenuChildren($_SERVER['REQUEST_URI'])
        ];

        try {
            list($page, $content) = $this->page_service->data($_SERVER['REQUEST_URI']);
        } catch (PageNotFound $exception){
            # Try to redirect
            $this->redirect_service->redirect($_SERVER['REQUEST_URI']);

            # Try to find data from "lists"
            list($page, $content) = $this->redirect_service->list($_SERVER['REQUEST_URI']);

            if (empty($page)) {
                echo $this->render_service->page404($menu);
                return;
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            echo $this->render_service->error($menu);
            return;
        }

        try {
            $this->render_service->load($page, $menu);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            echo $this->render_service->error($menu, 'Failed load template', 'Error loading template');
            return;
        }

        echo $this->render_service->render($menu, $content, $page);
    }
}