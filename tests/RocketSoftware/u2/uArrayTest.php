<?php

class uArrayTest extends \PHPUnit_Framework_TestCase {
  /**
   * Test setting of a multi-valued array.
   */
  public function testSetValue() {
    $v = new RocketSoftware\u2\uArray();

    $v[0] = "1\xfd2";

    $this->assertEquals("1\xfd2", (string)$v);

    return $v;
  }

  /**
   * @depends testSetValue
   */
  public function testAddValue($v) {
    $v[4] = '4';

    $this->assertEquals("1\xfd2\xfd\xfd4", (string)$v);

    return $v;
  }

  /**
   * @depends testAddValue
   */
  public function testAddValueBetween($v) {
    $v[3] = '3';

    $this->assertEquals("1\xfd2\xfd3\xfd4", (string)$v);

    return $v;
  }

  /**
   * @depends testAddValueBetween
   */
  public function testSetSubValue($v) {
    $v[1][2] = '1,2';

    $this->assertEquals("1\xfc1,2\xfd2\xfd3\xfd4", (string)$v);

    return $v;
  }

  /**
   * @depends testSetSubValue
   */
  public function testUnsetSubValue($v) {
    unset($v[1][2]);

    $this->assertEquals("1\xfd2\xfd3\xfd4", (string)$v);
    return $v;
  }

  /**
   * @depends testUnsetSubValue
   */
  public function testValueNotExists($v) {
    $this->assertFalse(isset($v[5]));

    return $v;
  }

  /**
   * @depends testValueNotExists
   */
  public function testDelValue($v) {
    $v[7] = '7';
    $v->del(5);

    $this->assertEquals("1\xfd2\xfd3\xfd4\xfd\xfd7", (string)$v);

    return $v;
  }

  /**
   * @depends testDelValue
   */
  public function testInsValue($v) {
    $v->ins('6', 6);

    $this->assertEquals("1\xfd2\xfd3\xfd4\xfd\xfd6\xfd7", (string)$v);

    return $v;
  }

  /**
   * @depends testInsValue
   */
  public function testAppendValue($v) {
    $v[] = '8';

    $this->assertEquals("1\xfd2\xfd3\xfd4\xfd\xfd6\xfd7\xfd8", (string)$v);

    return $v;
  }

  public function testSetArray() {
    $v = new RocketSoftware\u2\uArray();

    $v[0] = array('1', '2', '3');

    $this->assertEquals("1\xfe2\xfe3", (string)$v);

    return $v;
  }
}