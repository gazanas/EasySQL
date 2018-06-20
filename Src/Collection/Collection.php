<?php

namespace Src\Collection;

/**
 * Collection Class
 *
 * @version 0.1.0
 **/
class Collection implements \IteratorAggregate, \JsonSerializable
{


    /**
     * Construct a collection object, adding the wished number
     * of array elements.
     *
     * @param array ...$elements The elements to add in the Collection.
     */
    public function __construct(array ...$elements)
    {
        foreach ($elements as $element) {
            $this->addCollection($element);
        }
    }


    /**
     * Get a Collection object
     *
     * @param integer $index The index of the Collection StdClass element to return.
     *
     * @return array The selected element of the collection.
     *
     * @throws CollectionException The requested Collection element was not found or was empty.
     */
    public function getCollection(int $index)
    {
        if (isset($this->{$index}) === true && empty((array) $this->{$index}) === false) {
            return $this->{$index};
        } else {
            throw new CollectionException('The requested collection does not exist or is empty.');
        }
    }


    /**
     * Count the attributes of the Collection object
     *
     * @return integer size
     */
    public function count()
    {
        return count((array) $this);
    }


    /**
     * Add a new array element in the Collection object
     *
     * @param array $element New element to add on an existing Collection.
     *
     * @return void
     */
    public function addCollection(array $element)
    {
        $this->{count((array) $this)} = $element;
    }


    /**
     * Turn an array to an StdClass object
     *
     * @param array $element The array element to convert.
     *
     * @return StdClass The StdClass object.
     
    private function arrayToObject(array $element)
    {
        return json_decode(json_encode((object) $element), false);
    }
*/

    /**
     * Delete an array element of the Collection object
     *
     * @param integer $index The index of the Collection element to delete.
     *
     * @return Collection $collection
     */
    public function deleteCollection(int $index)
    {
        unset($this->{$index});
        return $this;
    }


    public function getIterator()
    {
        return new \ArrayIterator((array) $this);
    }


    public function jsonSerialize()
    {
        return $this;
    }
}
