<?php


namespace App\Service;


class RedirectService extends ConfigService
{

    public function __construct($website_path)
    {
        parent::__construct($website_path);
    }

    public function action(string $path)
    {
        $config = $this->load('redirect.yaml');

        require_once $this->website_path . '/api/' . $config['api'] . '/index.php';

        $redirect = new \RedirectPage($this->website_path, $config);

        $redirect->check([$path]);
    }

    public function list(string $path): array
    {
        $config = $this->load('lists.yaml');
        foreach ($config as $item) {
            if (preg_match($item['pattern'], $path, $matches)) {
                require_once $this->website_path . '/lists/' . $item['folder'] . '/index.php';
                $class_name = "\Package\\" .$item['class'];
                $instance = new $class_name();
                return [$item, $instance->check($matches)];
            }
        }
        return [null, null];
    }
}