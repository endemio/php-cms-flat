<?php


namespace App\Service;


class TransformService extends ConfigService
{

    public function redirect(string $path): bool
    {
        $config = $this->loadConfig('redirect.yaml');

        foreach ($config as $item) {

            #print $item['pattern'].' '. $path.PHP_EOL;

            if (preg_match($item['pattern'], $path, $matches)) {

                if (!empty($item['class'])) {
                    $class_name = "\Redirect\\" . $item['class'];
                    require_once $this->website_path . '/transforms/redirect/' . $item['folder'] . '/index.php';
                    $instance = new $class_name($this->website_path, $item);
                    return $instance->check([$path, $item['pattern']]);
                } elseif ($item['type'] == 'direct') {
                    header(sprintf("Location: %s", $item['target']));
                    return true;
                }
            }
        }
        return false;
    }

    public function list(string $path): array
    {
        $config = $this->loadConfig('lists.yaml');

        foreach ($config as $item) {

            #print $item['pattern'].' '. $path.PHP_EOL;

            if (preg_match($item['pattern'], $path, $matches)) {
                if (is_file($this->website_path . '/transforms/list/' . $item['folder'] . '/index.php')) {
                    require_once $this->website_path . '/transforms/list/' . $item['folder'] . '/index.php';
                    $class_name = "\Lists\\" . $item['class'];
                    $instance = new $class_name();
                    return [$item, $instance->check($matches)];
                }
            }
        }
        return [null, null];
    }
}