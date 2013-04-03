<?php

namespace RocketSoftware\u2;

class uArrayContainer implements \ArrayAccess, uAssocArraySource {
  private $data = array();

  public function __construct($values = array()) {
    foreach ($values as $key => $value) {
      $this->data[$key] = new uArray($value);
    }
  }
  
  public function offsetExists($delta) {
    return isset($this->data[$delta]);
  }
  
  public function offsetGet($delta) {
    return $this->get($delta);
  }
  
  public function offsetSet($delta, $value) {
    $this->data[$delta] = new uArray($value);
  }
  
  public function offsetUnset($delta) {
    unset($this->data[$delta]);
  }
  
  public function fieldExists($delta) {
    return isset($this[$delta]);
  }
  
  public function get($delta) {
    return $this->data[$delta];
  }
  
  /**
   * Fetch an associated array of the defined fields
   */
  public function fetchAssoc() {
    $fields = func_get_args();
    $key = NULL;

    if (is_array($fields[0])) {
      $key = isset($fields[1]) ? $fields[1] : NULL;
      $fields = $fields[0];
    }

    return new uAssocArray($this, $fields, $key);
  }
}