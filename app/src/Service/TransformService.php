<?php


namespace App\Service;


class TransformService extends ConfigService
{

    public function __construct($website_path)
    {
        parent::__construct($website_path);
    }

    public function redirect(string $path): void
    {
        $config = $this->load('redirect.yaml');

        foreach ($config as $item) {
            if (preg_match($item['pattern'], $path, $matches)) {

                if (!empty($item['class'])) {
                    $class_name = "\Redirect\\" . $item['class'];
                    require_once $this->website_path . '/transforms/redirect/' . $item['folder'] . '/index.php';
                    $instance = new $class_name($this->website_path, $item);
                    $instance->check([$path, $item['pattern']]);
                } elseif ($item['type'] == 'direct') {
                    header(sprintf("Location: %s", $item['target']));
                }
            }
        }
    }

    public function list(string $path): array
    {
        $config = $this->load('lists.yaml');
        foreach ($config as $item) {
            if (preg_match($item['pattern'], $path, $matches)) {
                require_once $this->website_path . '/transforms/list/' . $item['folder'] . '/index.php';
                $class_name = "\Lists\\" .$item['class'];
                $instance = new $class_name();
                return [$item, $instance->check($matches)];
            }
        }
        return [null, null];
    }
}