<?php

namespace RocketSoftware\u2;

use RocketSoftware\u2\uAssocArrayItem;
use RocketSoftware\u2\uAssocArraySource;
use RocketSoftware\u2\uException;

class uAssocArray implements \ArrayAccess, \Countable, \Iterator {
  private $source = NULL;
  private $fields = array();
  private $key_field = NULL;
  private $iterator_position = 0;

  public function __construct(uAssocArraySource $source, $fields, $key_field = NULL) {
    $this->source = $source;
    $this->fields = $fields;
    $this->key_field = $key_field;

    foreach ($this->fields as $field) {
      if (!$this->source->fieldExists($field)) {
        throw new uException("{$field} is not a valid field");
      }
    }

    if ($key_field) {
      if (!$this->source->fieldExists($key_field)) {
        throw new uException("{$key_field} is not a valid field");
      }
    }
  }

  public function get($delta) {
    if (isset($this->key_field)) {
      $keys = $this->source->get($this->key_field);

      if (!isset($delta)) {
        $delta = $keys->max()+1;
      }

      if (($position = $keys->searchUnique($delta)) !== FALSE) {
        return new uAssocArrayItem($this->source, $this->fields, $position, $this->key_field, $delta);
      }
      return new uAssocArrayItem($this->source, $this->fields, NULL, $this->key_field, $delta);
    }
    return new uAssocArrayItem($this->source, $this->fields, $delta);
  }

  public function search($needle, $field) {
    foreach ($this as $key => $data) {
      if ($data[$field] == $needle) {
        return $key;
      }
    }
    return FALSE;
  }

  public function getArrayCopy() {
    $array = array();

    if (isset($this->key_field)) {
      foreach ($this->source->get($this->key_field)->getValues() as $key => $delta) {
        $array[$key] = $this->get($key);
      }
    }
    else {
      for ($i = 1; $i <= $this->count(); $i++) {
        $array[$i] = $this->get($i);
      }
    }

    return $array;
  }

  public function getLastKey() {
    if ($this->key_field) {
      $keys = $this->source->get($this->key_field)->getValues();
      return end($keys);
    }
    return count($this);
  }

  public function getLast() {
    return $this->get($this->getLastKey());
  }

  public function offsetExists($delta) {
    if (isset($this->key_field)) {
      $keys = $this->source->get($this->key_field);
      if (($delta = $this->source->get($this->key_field)->searchUnique($delta)) === FALSE) {
        return FALSE;
      }
    }
    foreach ($this->fields as $field) {
      $value = $this->source->get($field);
      if (!empty($value[$delta])) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function offsetGet($delta) {
    return $this->get($delta);
  }

  public function offsetSet($delta, $value) {
    $this->get($delta)->set($value);
  }

  public function offsetUnset($delta) {
    if ($delta == $this->key()) {
      $this->iterator_position--;
    }

    if (isset($this->key_field)) {
      if (($delta = $this->source->get($this->key_field)->searchUnique($delta)) === FALSE) {
        return;
      }
    }

    foreach ($this->fields as $field) {
      $this->source->get($field)->del($delta);
    }

    if (isset($this->key_field) && !in_array($this->key_field, $this->fields)) {
      $this->source->get($this->key_field)->del($delta);
    }
  }

  public function count() {
    $max = 0;

    foreach ($this->fields as $field) {
      $count = count($this->source->get($field));

      $max = $count > $max ? $count : $max;
    }

    return $max;
  }

  public function current() {
    return $this->get($this->key());
  }

  public function key() {
    if (isset($this->key_field)) {
      $keys = $this->source->get($this->key_field);

      return (string)$keys[$this->iterator_position];
    }
    return $this->iterator_position;
  }

  public function next() {
    if (isset($this->key_field)) {
      $keys = $this->source->get($this->key_field)->getValues();
      $key_values = array_keys($keys);
      if (($pos = array_search($this->iterator_position, $key_values)) !== FALSE) {
        $pos++;
        if (isset($key_values[$pos])) {
          $this->iterator_position = $key_values[$pos];
        }
        else {
          $this->iterator_position = $this->count()+1;
        }
      }
      else {
        $this->iterator_position = $this->count()+1;
      }
    }
    else {
      $this->iterator_position++;
    }
  }

  public function rewind() {
    $this->iterator_position = 1;
  }

  public function valid() {
    return $this->iterator_position <= $this->count();
  }
}