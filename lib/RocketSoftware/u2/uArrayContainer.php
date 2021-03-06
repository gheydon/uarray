<?php

namespace RocketSoftware\u2;

class uArrayContainer implements \ArrayAccess, \Iterator, uAssocArraySource {
  private $data = array();
  private $options = array();
  private $current = NULL;

  public function __construct($values = array(), $options = array()) {
    $this->options = $options;

    if (isset($values)) {
      foreach ($values as $key => $value) {
        if ($value instanceof uArray) {
          $this->data[$key] = $value;
        }
        else {
          $this->data[$key] = new uArray($value, $this->options);
        }
      }
    }
  }

  public function offsetExists($delta) {
    return isset($this->data[$delta]);
  }

  public function offsetGet($delta) {
    return $this->get($delta);
  }

  public function offsetSet($delta, $value) {
    $this->set($delta, $value);
  }

  public function offsetUnset($delta) {
    unset($this->data[$delta]);
  }

  public function fieldExists($delta) {
    return array_key_exists($delta, $this->data);
  }

  public function current() {
    return $this->data[$this->current];
  }

  public function key() {
    return $this->current;
  }

  public function next() {
    $keys = array_keys($this->data);

    if (($position = array_search($this->current, $keys)) !== FALSE) {
      $position++;

      $this->current = isset($keys[$position]) ? $keys[$position] : NULL;
    }
  }

  public function rewind() {
    $keys = array_keys($this->data);
    $this->current = reset($keys);
  }

  public function valid() {
    return isset($this->data[$this->current]);
  }

  public function get($delta) {
    return isset($this->data[$delta]) ? $this->data[$delta] : NULL;
  }

  public function set($delta, $value) {
    if ($value instanceof uArray) {
      $this->data[$delta] = $value;
    }
    else {
      $this->data[$delta] = new uArray($value, $this->options);
    }
    $this->data[$delta]->taintArray();
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

  /**
   * Allows for the bulding of the query string based upon the data withing the container.
   *
   * @param BOOL $change_only.
   *  If enabled will only export fields which have been changed since the creation of the object.
   *
   * @param array $required.
   *  a list of fields which should always be included with the returned array
   *
   * @param $exclude.
   *  a regular expression of what fields which should be excluded. However $additional always has priority.
   *  Note that this is a full regular expression including delimiters and flags.
   *
   */
  public function http_build_query($changed_only = TRUE, array $required = NULL, $exclude = '') {
    $data = array();

    $required = isset($required) ? $required : array();

    foreach ($this->data as $key => $value) {
      if ((($changed_only && $value->isTainted()) || !$changed_only || in_array($key, $required))) {
        if (!in_array($key, $required) && $exclude && preg_match($exclude, $key)) {
          continue;
        }
        $data[$key] = html_entity_decode($value, ENT_QUOTES);
      }
    }

    return http_build_query($data);
  }

  /**
   * Check if any of the variables have been tainted
   */
  public function isTainted() {
    foreach ($this->data as $key => $value) {
      if ($value->isTainted()) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Reset all the taint flags of all the items.
   */
  public function resetTaintFlag() {
    array_map(function ($uArray) {
      $uArray->resetTaintedFlag();
    }, $this->data);
  }
}