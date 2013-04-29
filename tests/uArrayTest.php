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

  public function testSetSubValueMark() {
    $v = new RocketSoftware\u2\uArray("1\xfc2");

    $this->assertEquals("1\xfc2", (string)$v);
    $this->assertEquals('1', (string)$v[1][1]);
    $this->assertEquals('2', (string)$v[1][2]);

    return $v;
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage There can be only numerical keyed items in the array
   */
  public function testInsTextDelta() {
    $v = new RocketSoftware\u2\uArray();

    $v->ins('new', 'new');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Can only insert positive keyed items in the array
   */
  public function testInsNegativeDelta() {
    $v = new RocketSoftware\u2\uArray();

    $v->ins('new', -2);
  }

  /**
   * @depends testInsValue
   */
  public function testAppendValue($v) {
    $v[] = '8';

    $this->assertEquals("1\xfd2\xfd3\xfd4\xfd\xfd6\xfd7\xfd8", (string)$v);

    return $v;
  }

  public function testGetOneValue() {
    $v = new RocketSoftware\u2\uArray('1');

    $this->assertEquals('1', $v[1]);

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

  public function testMax() {
    $values = array('1', '2', '3', "5\xfd6", 'max');
    $v = new RocketSoftware\u2\uArray($values);

    $this->assertEquals(max($values), $v->max());

    return $v;
  }

  public function testTainted() {
    $values = array('1', '2', '3', "5\xfd6", 'max');
    $v = new RocketSoftware\u2\uArray($values);
    $this->assertFalse($v->isTainted());

    $v[4][3] = '3';
    $this->assertTrue($v->isTainted());

    $values = array('1', '2', '3', "5\xfd6", 'max');
    $v = new RocketSoftware\u2\uArray($values);

    $a = $v[4][1];
    $this->assertFalse($v->isTainted());

    $a[] = '7';

    return $v;
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage There can be only numerical keyed items in the array [new]
   */
  public function testInvalidGetDelta() {
    $v = new RocketSoftware\u2\uArray();

    $a = $v['new'];
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage There can be only numerical keyed items in the input array
   */
  public function testInvalidGetDeltaArray() {
    $v = new RocketSoftware\u2\uArray(array('new' => 1));
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Too many levels created.
   */
  public function testTooManyLevel() {
    $v = new RocketSoftware\u2\uArray();

    $v[1][2][3] = "1\xfd2";
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage Too many levels created.
   */
  public function testTooManyLevelArray() {
    $v = new RocketSoftware\u2\uArray();

    $v[1][2][3] = array(1,2);
  }

  public function testAssocuArrayAssoc() {
    $record = '1]2]3]4]5^one]two]three]four]five';
    $v = new RocketSoftware\u2\uArray(strtr($record, array(']' => "\xfc", '^' => "\xfd")));

    $assoc = $v->fetchAssoc(array(2), 1);
    $test = array(
      1 => 'one',
      2 => 'two',
      3 => 'three',
      4 => 'four',
      5 => 'five',
    );

    foreach ($assoc as $k => $v) {
      $this->assertEquals((string)$v[2], $test[$k]);
    }
  }
}