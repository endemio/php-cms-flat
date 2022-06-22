<?php


namespace App\Tests\App\Services\Helper;


use App\Service\TreeManagingService;

class TreeManagingServiceTest extends \PHPUnit\Framework\TestCase
{

    private TreeManagingService $tree_service;

    const PARENT_ID = 'parent_id';
    const CHILD = 'child';

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        parent::setUp();

        $this->tree_service = new TreeManagingService();

        $this->tree_service->setTop('');
    }

//    public function testGenerateTree()
//    {
//        $input = $this->input();
//        $tree = $this->tree_service->transformArrayToTree($input, '',
//            self::PARENT_ID,self::CHILD);
//
//        $this->assertEquals('5', $tree[0][self::CHILD][0][self::CHILD][0][self::CHILD][1]['id']);
//    }

    public function testExtractTreePartByRootId1stLevel(){

        $input = $this->input();
        $tree = $this->tree_service->transformArrayToTree($input, '', self::PARENT_ID, self::CHILD);

        $input = $this->input();
        $new_tree = $this->tree_service->extractTreePartByRootId('1', $tree, $input, self::PARENT_ID, self::CHILD);

        print_r($new_tree);

//        $this->assertEquals('4', $new_tree[self::CHILD][0][self::CHILD][0]['id']);
//        $this->assertEquals('8', $new_tree[self::CHILD][1][self::CHILD][1]['id']);

    }

//    public function testExtractTreePartByRootId(){
//
//        $input = $this->input();
//        $tree = $this->tree_service->transformArrayToTree($input, '', self::PARENT_ID, self::CHILD);
//
//        $input = $this->input();
//        $new_tree = $this->tree_service->extractTreePartByRootId('2', $tree, $input, self::PARENT_ID, self::CHILD);
//
//        $this->assertEquals('4', $new_tree[self::CHILD][0][self::CHILD][0]['id']);
//        $this->assertEquals('8', $new_tree[self::CHILD][1][self::CHILD][1]['id']);
//
//    }
//
//    public function testTransformTreeToArray(){
//
//        $input = $this->input();
//        $tree = $this->tree_service->transformArrayToTree($input, '', self::PARENT_ID, self::CHILD);
//
//        $input = $this->input();
//        $new_tree = $this->tree_service->extractTreePartByRootId('2', $tree, $input, self::PARENT_ID, self::CHILD);
//
//        $result = $this->tree_service->transformTreeToArray($new_tree[self::CHILD], self::CHILD);
//
//        $this->assertCount(8, $result);
//
//    }
//
//    public function testTransformTreeToArray2(){
//
//        $input = $this->input();
//        $tree = $this->tree_service->transformArrayToTree($input, '', self::PARENT_ID, self::CHILD);
//
//        $input = $this->input();
//        $new_tree = $this->tree_service->extractTreePartByRootId('9', $tree, $input, self::PARENT_ID, self::CHILD);
//
//        $result = $this->tree_service->transformTreeToArray($new_tree, self::CHILD);
//
//        $this->assertCount(2, $result);
//
//    }


    private function input(): array
    {
        return [
            ['id' => '1', 'parent_id' => ''],
            ['id' => '2', 'parent_id' => '1'],
            ['id' => '3', 'parent_id' => '2'],
            ['id' => '4', 'parent_id' => '3'],
            ['id' => '5', 'parent_id' => '3'],
            ['id' => '6', 'parent_id' => '2'],
            ['id' => '7', 'parent_id' => '6'],
            ['id' => '8', 'parent_id' => '6'],
            ['id' => '9', 'parent_id' => '8'],
            ['id' => '10', 'parent_id' => '8'],
        ];
    }

}