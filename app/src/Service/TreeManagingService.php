<?php


namespace App\Service;


use App\Controller\MainController;

use Exception;

class TreeManagingService extends MainController
{

    const ID = 'id';

    private string $top = '/';

    /**
     * @param string $target_id
     * @param array $tree
     * @param array $list
     * @param string $index_parent
     * @param string $index_child
     * @return array
     */
    public function extractTreePartByRootId(string $target_id, array $tree, array $list, string $index_parent, string $index_child): array
    {

        #$this->logger->debug(sprintf('Start %s, Try to extract tree for %s', __FUNCTION__, $target_id),$this->Log(__FUNCTION__), $this->DEBUG());

        //$this->logger->debug(sprintf('Tree is %s, all elements %s', json_encode($tree, JSON_PRETTY_PRINT), json_encode($list, JSON_PRETTY_PRINT)), $this->Log(__FUNCTION__), $this->DEBUG());

        $root_id = $this->fetchRootElementId($target_id, $list, $index_parent);

        //print 'Root ->' . $root_id . PHP_EOL;

        #$this->logger->debug(sprintf('Root element id is %s', $root_id), $this->Log(__FUNCTION__), $this->DEBUG());

        # Get tree vetka by root id
        try {
            $target_tree = array_values(array_filter($tree, function ($item) use ($root_id) {
                return isset($item[self::ID]) ? $item[self::ID] === $root_id : false;
            }));

            if(count($target_tree)){
                $target_tree = $target_tree[0];
            } else {
                $target_tree = $tree[0];
            }

        } catch (Exception $exception) {
            print $exception->getMessage();
            #$this->logger->exception($exception, $this->DEBUG());
            $target_tree = $tree[0];
        }

        //print_r($target_tree);

        #$this->logger->debug(sprintf('Target tree with element is %s', json_encode($target_tree, JSON_PRETTY_PRINT)), $this->Log(__FUNCTION__), $this->DEBUG());

        if (strcmp($target_tree[self::ID], $target_id) !== 0) {
            # If searching element not root element of tree - search this element in branches
            $target_tree = $this->sliceTree(isset($target_tree[$index_child]) ? $target_tree[$index_child] : [$target_tree], $target_id, $index_child);
            //$target_tree = $this->transformTreeToArray($target_tree);
        }

        #$this->logger->debug(sprintf('Complete extract part of tree for %s, tree is %s',$target_id, json_encode($target_tree, JSON_PRETTY_PRINT)), $this->Log(__FUNCTION__), $this->DEBUG());

        return $target_tree;

    }

    /**
     * @param array $list
     * @param string $path
     * @return array
     * @throws Exception
     */
    public function fetchAllActive(array $list, string $path): array
    {

        $parent = $path;

        $result = [];

        $counter = 0;

        while ($parent !== '/' && $counter < 10) {

            $items = array_values(array_filter($list, function ($item) use ($parent) {
                return $item['id'] === $parent;
            }));

            if (count($items)) {
                $result[$this->fetchCurrentLevel($parent)] = $items[0]['id'];
                $parent = $items[0]['parent'];
            }

            $counter++;
        }

        if ($counter > 9) {
            throw new Exception('Too much rounds');
        }

        return $result;


    }

    public function fetchCurrentLevel(string $path): int
    {
        $items_split = preg_split('/\//', $path);
        return count($items_split) - 2;
    }


    public function sliceTree(array $tree, string $branchId, string $index_child): array
    {
        // check all branches
        foreach ($tree as $branch) {
            // have we found the correct branch?
            #print('Child is ' . json_encode($branch, JSON_PRETTY_PRINT));

            if ($branch[self::ID] == $branchId) return $branch;

            // check the children
            if (isset($branch[$index_child])) {
                $slice = $this->sliceTree($branch[$index_child], $branchId, $index_child);
                if (count($slice)) return $slice;
            }
        }

        // nothing was found
        return []; #todo: check and fix, before was "null"
    }


    /**
     * @param array $elements
     * @param null $parentId
     * @param string $index_parent
     * @param string $index_child
     * @return array
     */
    public function transformArrayToTree(array &$elements, $parentId, string $index_parent, string $index_child): array
    {
        $branch = array();

        foreach ($elements as $key => $element) {
            if ($element[$index_parent] == $parentId) {
                $children = $this->transformArrayToTree($elements, $element[self::ID], $index_parent, $index_child);
                if ($children) {
                    $element[$index_child] = $children;
                }
                array_push($branch, $element);
                unset($elements[$key]);
            }
        }
        return $branch;
    }

    /**
     * @param array $arr
     * @param string $index_child
     * @return array
     */
    public function transformTreeToArray(array $arr, string $index_child): array
    {
        $result = [];

        foreach ($arr as $item) {
            if (isset($item[$index_child])) {
                $result = array_merge($result, $this->transformTreeToArray($item[$index_child], $index_child));
                unset($item[$index_child]);
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param string $id
     * @param array $array
     * @param string $index_parent
     * @return string
     */
    public function fetchRootElementId(string $id, array $array, string $index_parent): string
    {
        $has_hq = true;
        $counter = 0;

        while ($has_hq) {

            try {
                $companies = array_values(array_filter($array, function ($item) use ($id) {
                    return $item[self::ID] === $id;
                }));

                if (!count($companies)){
                   return '';
                }

                $company = $companies[0];

            } catch (Exception $exception) {
                return '';
            }

            if (strcmp($company[$index_parent], $this->top) !== 0) {
                $id = $company[$index_parent];
            } else {
//                print 'Equal'.PHP_EOL;
                return $id;
            }

//            print $id.PHP_EOL;


            $counter++;

            #todo: what is that???
            if ($counter > 10) {
                $has_hq = false;
            }

        }

        return $id;
    }

    public function setTop(string $top)
    {
        $this->top = $top;
    }
}