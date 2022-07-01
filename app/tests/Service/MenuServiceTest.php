<?php

namespace App\Tests\Service;

class MenuServiceTest extends \PHPUnit\Framework\TestCase
{

    private \App\Service\MenuService $service;

    private string $path = '/opt/project/sites/localhost';

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new \App\Service\MenuService($this->path);

    }


    public function testGenerateMenuYaml(){

        $this->service->generateMenuYaml();

        $result = $this->service->fetchFullMenu();

        $this->assertCount(1, array_keys($result));

    }

    public function testFetchMenu(){

        $result = $this->service->fetchFullMenu();

        $this->assertCount(1, array_keys($result));

    }

    public function testFetchMenuCurrent1stLevel(){

        $result = $this->service->fetchMenuCurrentLevel('/test1');

        print_r($result);

        $this->assertCount(2, array_keys($result));
        $this->assertEquals('/test1', $result[0]['id']);

    }

    public function testFetchMenuCurrent2ndLevel(){

        $path = '/test1/test3';

        $tree = $this->service->fetchMenuCurrentLevel($path);

        $top = $this->service->fetchMenuTopLevel($path);

        $this->assertTrue($top[0]['active']);

        $this->assertCount(2, array_keys($tree));

        $tree = $this->service->fetchMenuCurrentLevel('/test4/test3');

        $this->assertCount(3, array_keys($tree));

    }

    public function testFetchMenuCurrent3rdLevel(){

        $path = '/test4/test5/test7';

        $tree = $this->service->fetchMenuCurrentLevel($path);

        $this->assertCount(1, array_keys($tree));

        $top = $this->service->fetchMenuTopLevel($path);

        $this->assertEquals(2, count($top));
    }

    public function testFetchMenu2ndLevelByCurrent3rdLevel(){

        $path = '/test4/test5/test7';

        $result = $this->service->fetchTargetLevel($path,2);

        $this->assertEquals(3, count($result));

    }

    public function testFetchMenuTopLevel(){

        $result = $this->service->fetchMenuTopLevel('/');

        $this->assertCount(2, array_keys($result));

    }

    public function testFetchActive(){

        $path = '/test4/test5/test7';

        $result = $this->service->fetchActive($path);

        $this->assertEquals('/test4',$result[0]);
        $this->assertEquals('/test4/test5',$result[1]);

    }
}