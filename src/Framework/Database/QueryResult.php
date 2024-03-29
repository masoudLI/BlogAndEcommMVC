<?php

namespace Framework\Database;

use ArrayAccess;
use Countable;
use Iterator;

class QueryResult implements ArrayAccess, Iterator, Countable
{

    /**
     * @var array Les enregistrements
     */
    private $records;

    /**
     * @var null|string Entité à utiliser pour hydrater nos objets
     */
    private $entity;

    /**
     * @var int Index servant à l'itération
     */
    private $index = 0;

    /**
     * @var array Sauvegarde les enregistrements déjà hydratés
     */
    private $hydratedRecords = [];


    public function __construct($records, ?string $entity = null)
    {
        $this->entity = $entity;
        $this->records = $records;
    }

    public function get(int $index)
    {
        if ($this->entity) {
            if (!isset($this->hydratedRecords[$this->index])) {
                $this->hydratedRecords[$this->index] = Hydrator::hydrate($this->records[$index], $this->entity);
            }
            return $this->hydratedRecords[$this->index];
        }
        return $this->entity;
    }



    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(): mixed
    {
        return $this->get($this->index);
    }



    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): int|string|null
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->records[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @throws \Exception
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        throw new \Exception("Error Processing Request");
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @throws \Exception
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        throw new \Exception("Error Processing Request");
    }

    public function count(): int
    {
        return count($this->index);
    }

    public function toArray()
    {
        $records = [];
        foreach ($this->records as $k => $v) {
            $records[] = $this->get($k);
        }
        return $records;
    }


    /**
     * Get les enregistrements
     *
     * @return  array
     */ 
    public function getRecords()
    {
        return $this->records;
    }
}
