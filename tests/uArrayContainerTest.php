<?php

use RocketSoftware\u2\uArrayContainer;

class uArrayContainerTest extends \PHPUnit_Framework_TestCase {
  
  public function testuArrayContainer() {
    $data = array(
      'id' => array(100,200,300,400,500),
      'values' => array('value 1', 'value 2', 'value 3', 'value 4', 'value 5'),
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
  }
}