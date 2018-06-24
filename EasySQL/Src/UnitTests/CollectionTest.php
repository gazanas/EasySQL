<?php

namespace EasySQL\Src\UnitTests;

use PHPUnit\Framework\TestCase;

use EasySQL\Src\Collection as Collection;

class CollectionTest extends TestCase
{

    private $collection;


    public function setUp()
    {
        $this->collection = new Collection\Collection(
            [
                'username' => 'test',
                'phone' => '202-555-0121'
            ],
            [
                'username' => 'test2',
                'phone' => '202-555-0162'
            ]
        );

    }//end setUp()


    public function tearDown()
    {
        unset($this->collection);

    }//end tearDown()


    public function testGetACollectionOfData()
    {
        $expected = [
            'username' => 'test',
            'phone' => '202-555-0121'
        ];

        $data = $this->collection->getCollection(0);

        $this->assertEquals($data, $expected);

    }//end testGetACollectionOfData()


    public function testThrowExceptionWhenCollectionIsEmpty()
    {
        $expected = new Collection\Collection();

        $this->expectException(Collection\CollectionException::class);

        $expected->getCollection(0);

    }//end testThrowExceptionWhenCollectionIsEmpty()


    public function testCountReturnsTheRightNumberOfArraysInACollection()
    {
        $count = $this->collection->count();

        $this->assertEquals(2, $count);

    }//end testCountReturnsTheRightNumberOfArraysInACollection()


    public function testAddsANewArrayOfElementsToAnExistingCollection()
    {
        $this->collection->addCollection(['username' => 'test3', '202-555-0153']);

        $expected = [
            'username' => 'test3',
            '202-555-0153'
        ];

        $this->assertEquals($this->collection->getCollection(2), $expected);

    }//end testAddsANewArrayOfElementsToAnExistingCollection()


    public function testDeletesAnExistingArrayOfElementsFromACollection()
    {
        $this->collection->deleteCollection(1);

        $count = $this->collection->count();

        $this->assertEquals(1, $count);

    }//end testDeletesAnExistingArrayOfElementsFromACollection()


}//end class
