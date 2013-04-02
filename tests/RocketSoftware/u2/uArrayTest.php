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
  
  public function testInsOnZero() {
    $v = new RocketSoftware\u2\uArray('1', array('delimiter' => "\xfd"));

    $v->ins('6', 6);
    $this->assertEquals("1\xfd\xfd\xfd\xfd\xfd6", (string)$v);

    return $v;
  }
  
  public function testCounter() {
    $v = new RocketSoftware\u2\uArray("1\xfd2\xfd3\xfd4\xfd5\xfd6\xfd7\xfd8\xfd9");
    
    $this->assertEquals(9, count($v));
    
    $v[] = '10';
    
    $this->assertEquals(10, count($v));
    
    $v[20] = '20';
    $this->assertEquals(20, count($v));
    
    return $v;
  }
  
  public function testEmptyCount() {
    $v = new RocketSoftware\u2\uArray("");

    $this->assertEquals(0, count($v));
    
    return $v;
  }
  
  public function testSingleCount() {
    $v = new RocketSoftware\u2\uArray("1");

    $this->assertEquals(1, count($v));
    
    return $v;
  }
  
  public function testInterator() {
    $v = new RocketSoftware\u2\uArray("1\xfd2\xfd3\xfd4\xfd5\xfd6\xfd7\xfd8\xfd9");
    
    $i = $j = 1;
    foreach ($v as $key => $value) {
      $this->assertEquals($i++, (string)$value);
      $this->assertEquals($j++, $key);
    }
    
    return $v;
  }
  
  public function testAddNull() {
    $v = new RocketSoftware\u2\uArray("1\xfd2\xfd3\xfd4\xfd5\xfd6\xfd7\xfd8\xfd9");
    
    $v[20] = '';
    $this->assertEquals("1\xfd2\xfd3\xfd4\xfd5\xfd6\xfd7\xfd8\xfd9", (string)$v);
    
    $v[9] = '';
    $this->assertEquals("1\xfd2\xfd3\xfd4\xfd5\xfd6\xfd7\xfd8", (string)$v);
    
    return $v;
  }
  
  /**
   * @depends testAddNull
   */
  public function testSearch($v) {
    $this->assertEquals(5, $v->search(5));
    $this->assertEquals('5', (string)$v[$v->search(5)]);

    return $v;
  }

  /**
   * @depends testAddNull
   */
  public function testUniqueSearch($v) {
    $v[14] = 'abc';
    
    $this->assertEquals(14, $v->searchUnique('abc'));
    
    $this->assertFalse($v->searchUnique('def'));
    
    $v[15] = 'abc';
    $this->assertEquals(15, $v->searchUnique('abc'));
    
    return $v;
  }
  
  public function testUniqueSearchOnEmpty() {
    $v = new RocketSoftware\u2\uArray();
    
    $this->assertFalse($v->searchUnique('def'));
    
    return $v;
  }

  public function testIssetZero() {
    $v = new RocketSoftware\u2\uArray("1");
    
    $this->assertTrue(isset($v[0]));
  }
}