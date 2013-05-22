<?php

use RocketSoftware\u2\uArrayContainer;

class uArrayContainerTest extends \PHPUnit_Framework_TestCase {

  public function testuArrayContainer() {
    $data = array(
      'id' => array(100,200,300,400,500),
      'values' => new RocketSoftware\u2\uArray(array('value 1', 'value 2', 'value 3', 'value 4', 'value 5')),
    );
    $container = new uArrayContainer($data);

    return $container;
  }

  /**
   * @depends testuArrayContainer
   */
  public function testGetField($container) {
    $v = $container['id'];

    $this->assertInstanceOf('RocketSoftware\u2\uArray', $v);
    $this->assertEquals("100\xfe200\xfe300\xfe400\xfe500", (string)$v);
  }

  /**
   * @depends testuArrayContainer
   */
  public function testSetField($container) {
    $container['next'] = array('abc', 'def', 'ghi', 'jkl', 'mno');

    $v = $container['next'];
    $this->assertInstanceOf('RocketSoftware\u2\uArray', $v);
    $this->assertEquals("abc\xfedef\xfeghi\xfejkl\xfemno", (string)$v);

    $this->assertTrue(isset($container['next']));
    unset($container['next']);
    $this->assertFalse(isset($container['next']));

    $value = new RocketSoftware\u2\uArray(array('abc', 'def', 'ghi', 'jkl', 'mno'));
    $container['next'] = $value;

    $this->assertTrue(isset($container['next']));
  }
  
  public function testInterator() {
    $data = array(
      'a1' => 1,
      'a2' => 2,
      'a3' => 3,
    );
    $container = new uArrayContainer($data);
    
    foreach ($container as $key => $value) {
      $keys = array_keys($data);
      $current = array_shift($keys);
      
      $this->assertEquals($key, $current);
      $record = array_shift($data);
      $this->assertEquals((string)$value, $record);
    }
  }
  
  public function testBuildHttpQuery() {
    $data = array(
      'a1' => 1,
      'a2' => 2,
      'a3' => 3,
    );
    $container = new uArrayContainer($data);
    
    $this->assertEquals($container->http_build_query(FALSE), 'a1=1&a2=2&a3=3');
    
    $container->resetTaintFlag();
    $container['a2'] = '22';
    
    $this->assertEquals($container->http_build_query(), 'a2=22');
    $this->assertEquals($container->http_build_query(FALSE, NULL, '/a2/i'), 'a1=1&a3=3');
  }
  
  public function testTainted() {
    $data = array(
      'id' => array(100,200,300,400,500),
      'values' => new RocketSoftware\u2\uArray(array('value 1', 'value 2', 'value 3', 'value 4', 'value 5')),
    );
    $container = new uArrayContainer($data);
    $container->resetTaintFlag();
    
    $this->assertFalse($container->isTainted());
    
    $container['a1'] = 'abc';
    
    $this->assertTrue($container->isTainted());
  }
}