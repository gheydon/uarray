<?php

use RocketSoftware\u2\uArrayContainer;

class uAssocArrayItemTest extends \PHPUnit_Framework_TestCase {
  private $container;

  public function setUp() {
    $data = array(
      'id' => array(100,200,300,400,500, 600),
      'v1' => array('value 1', 'value 2', 'value 3', 'value 4', 'value 5', array('1', '2', '3', '4', '5')),
      'v2' => array('value 1', 'value 2', 'value 3', 'value 4', 'value 5', array('100', '200', '300', '400', '500')),
    );
    $this->container = new uArrayContainer($data);
  }
  
  public function testFetchAssoc() {
    $assoc = $this->container->fetchAssoc(array('v1', 'v2'), 'id');
    
    $this->assertEquals(6, $assoc[600]->getDelta());
    
    $new = $assoc[600]->fetchAssoc(array('v2'), 'v1');
    
    $this->assertEquals(3, $new[3]->getDelta());
    
    $this->assertTrue(isset($assoc[600]));
    
    
    foreach ($assoc[600] as $key => $value) {
      $this->assertTrue(in_array($key, array('v1', 'v2')));
    }
    
    foreach ($new as $key => $value) {
      $this->assertEquals($key * 100, (string)$value['v2']);
    }
  }
  
  public function testUnsetItem() {
    $assoc = $this->container->fetchAssoc(array('v1', 'v2'), 'id');
    
    unset($assoc[300]['v1']);
    
    $this->assertEquals($assoc[300]['v1'], '');
  }
}
