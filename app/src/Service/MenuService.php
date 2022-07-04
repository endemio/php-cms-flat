<?php


namespace App\Service;



class MenuService extends DefaultService
{

    const MENU_YAML = 'menu.yaml';

    private string $config;

    private string $content;

    private array $menu = [];

    private TreeManagingService $tree;

    public function __construct(string $path)
    {

        $this->config = $this->checkFolderExist(sprintf('%s/%s',$path, self::FOLDER_CONFIG));

        $this->content = $this->checkFolderExist(sprintf('%s/%s',$path, self::FOLDER_CONFIG));

        $this->tree = new TreeManagingService();

        $this->tree->setTop('');
    }

    public function generateMenuYaml()
    {

//        try {
//            $tree = $this->getDirContents($this->content);
//        } catch (\Exception $exception) {
//            print $exception->getMessage().' '.$this->content;
//            $tree = [];
//        }
//
//        $result = $this->tree->transformArrayToTree($tree, '', 'parent', 'child');
//
//        $string = Yaml::dump($result);
//
//        file_put_contents(sprintf('%s/menu.yaml',$this->config), $string);
    }


    public function fetchFullMenu(): array
    {
        if (!count($this->menu)) {
            $this->fetchMenuFromYaml();
        }

        return $this->menu;
    }

    /**
     * @param string $path
     * @return array
     */
    public function fetchMenuCurrentLevel(string $path ): array
    {

        if (!count($this->menu)) {
            $this->fetchMenuFromYaml();
        }

        $list = $this->tree->transformTreeToArray($this->menu, 'child');

        if ($path !== '/') {

            #print $path;

            $tree = $this->tree->extractTreePartByRootId($path, $this->menu, $list, 'parent', 'child');

            #print_r($tree);

            $items = $this->tree->transformTreeToArray([$tree], 'child');

            #print_r($items);

            $target_elements = array_values(array_filter($items, function ($item) use ($path) {
                return (!empty($item))?$item['id'] === $path: false;
            }));

            if (count($target_elements)){
                $target_element = $target_elements[0]['parent'];
            } else {
                $target_element = '';
            }

            $same_level = array_values(array_filter($list, function ($item) use ($target_element) {
                return $item['parent'] === $target_element;
            }));

            $active = $this->fetchActive($path);

            if (!empty($active)) {
                foreach ($same_level as $key => $value) {
                    if (strcmp($active[$this->tree->fetchCurrentLevel($path)], $value['id']) == 0) {
                        $same_level[$key]['active'] = true;
                    }
                }
            }

            return $same_level;

        }

        return $this->fetchMenuTopLevel();
    }

    public function fetchMenuChildren($path): array
    {

        if (!count($this->menu)) {
            $this->fetchMenuFromYaml();
        }

        $list = $this->tree->transformTreeToArray($this->menu, 'child');

        $result = array_values(array_filter($list, function ($item) use ($path) {
            return $item['parent'] == $path;
        }));

        return $result;

    }


    /**
     * @param string $path
     * @return array
     */
    public function fetchMenuTopLevel(string $path = '/'): array
    {
        if (!count($this->menu)) {
            $this->fetchMenuFromYaml();
        }

        $list = $this->tree->transformTreeToArray($this->menu, 'child');

        $result = array_values(array_filter($list, function ($item) {
            return $item['parent'] === '/';
        }));

        $active = $this->fetchActive($path);

        if (empty($active)){
            return $result;
        }

        foreach ($result as $key => $value) {
            if (strcmp($active[0], $value['id']) === 0) {
                $result[$key]['active'] = true;
            }
        }

        return $result;
    }

    /**
     * @param string $path
     * @param int $level
     * @return array
     * @throws \Exception
     */
    public function fetchTargetLevel(string $path = '', int $level = 0): array
    {

        $result = $this->fetchActive($path);

        if (count($result) < $level){
            throw new \Exception('Level is more then exits tree levels');
        }

        return $this->fetchMenuCurrentLevel($result[$level-1]);
    }

    public function fetchActive(string $path): array
    {

        if (!count($this->menu)) {
            $this->fetchMenuFromYaml();
        }

        $list = $this->tree->transformTreeToArray($this->menu, 'child');

        try {
            return $this->tree->fetchAllActive($list, $path);
        }catch (\Exception $exception){
            return [];
        }

    }

    private function fetchMenuFromYaml()
    {

        if (!is_file(sprintf('%s/%s', $this->config,self::MENU_YAML))){
            $this->generateMenuYaml();
        }

        $content = file_get_contents(sprintf('%s/%s', $this->config,self::MENU_YAML));
        $this->menu = parent::getYaml($content);

    }

//    /**
//     * @param $dir
//     * @return array
//     * @throws \Exception
//     */
//    public function getDirContents($dir): array
//    {
//
//        $results = [];
//
//        $this->generateTree($dir, $results);
//
//        if (is_dir($dir)) {
//
//            $iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
//
//            foreach (new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST) as $item) {
//
//                if ($item->isDir()) {
//                    $this->generateTree($item, $results);
//                }
//            }
//        } else {
//            throw new \Exception('Not folder');
//        }
//
//        return $results;
//    }

//    /**
//     * @param string $item
//     * @param $results
//     */
//    private function generateTree(string $item, &$results)
//    {
//
//        try {
//            list($yaml, $contents) = $this->tree->parseMD($item . '/index.md');
//        } catch (\Exception $exception) {
//            $yaml['menu'] = null;
//        }
//
//        $path = str_replace('\\', '/', str_replace($this->content, '', $item));
//
//        $root = substr($path, 0, strrpos($path, '/'));
//
//        $results[strlen($path) ? $path : '/'] = [
//            'id' => strlen($path) ? $path : '/',
//            'menu' => $yaml['menu'],
//            'parent' => strlen($root) ? $root : (strlen($path) > 2 ? '/' : '')
//        ];
//    }

}