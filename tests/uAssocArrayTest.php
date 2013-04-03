<?php

use RocketSoftware\u2\uArrayContainer;

class uAssocArrayTest extends \PHPUnit_Framework_TestCase {
  
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
  public function testAccessAssoc($container) {
    $assoc = $container->fetchAssoc('id', 'values');

    $this->assertEquals(100 , (string)$assoc[1]['id']);
    $this->assertEquals('value 1' , (string)$assoc[1]['values']);
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testIndexedAssocArray($container) {
    $assoc = $container->fetchAssoc(array('values'), 'id');
    
    $this->assertEquals('value 1' , (string)$assoc[100]['values']);
  }
  
  /**
   * @depends testuArrayContainer
   * @expectedException Exception
   * @expectedExceptionMessage additional is not a valid field
   */
  public function testFieldNotExist($container) {
    $assoc = $container->fetchAssoc('id', 'values', 'additional');
  }
  
  /**
   * @depends testuArrayContainer
   * @expectedException Exception
   * @expectedExceptionMessage additional is not a valid field
   */
  public function testKeyNotExist($container) {
    $assoc = $container->fetchAssoc(array('id', 'values'), 'additional');
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testAddNewArray($container) {
    $assoc = $container->fetchAssoc(array('values'), 'id');
    
    $assoc[]['values'] = 'Value 6';
    
    $key = $assoc->getLastKey();
    $this->assertEquals("100\xfe200\xfe300\xfe400\xfe500\xfe{$key}", (string)$container['id']);
    $this->assertEquals("value 1\xfevalue 2\xfevalue 3\xfevalue 4\xfevalue 5\xfeValue 6", (string)$container['values']);
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testAddNewValue($container) {
    $assoc = $container->fetchAssoc(array('values'), 'id');
    
    $assoc[] = array('values' => 'Value 7');
    
    $key = $assoc->getLastKey();
    $this->assertEquals("Value 7", (string)$assoc[$key]['values']);
    $this->assertEquals("100\xfe200\xfe300\xfe400\xfe500\xfe501\xfe{$key}", (string)$container['id']);
    $this->assertEquals("value 1\xfevalue 2\xfevalue 3\xfevalue 4\xfevalue 5\xfeValue 6\xfeValue 7", (string)$container['values']);
    
    $assoc = $container->fetchAssoc('values', 'id');
    
    $assoc[] = array('values' => 'Value 8', 'id' => '800');
    
    $key = $assoc->getLastKey();
    $this->assertEquals("Value 8", (string)$assoc[$key]['values']);
    $this->assertEquals("100\xfe200\xfe300\xfe400\xfe500\xfe501\xfe502\xfe800", (string)$container['id']);
    $this->assertEquals("value 1\xfevalue 2\xfevalue 3\xfevalue 4\xfevalue 5\xfeValue 6\xfeValue 7\xfeValue 8", (string)$container['values']);
    
    $assoc[] = array('values' => 'Value 9');
    $key = $assoc->getLastKey(); 
    $this->assertEquals("100\xfe200\xfe300\xfe400\xfe500\xfe501\xfe502\xfe800", (string)$container['id']);
    $this->assertEquals("value 1\xfevalue 2\xfevalue 3\xfevalue 4\xfevalue 5\xfeValue 6\xfeValue 7\xfeValue 8\xfeValue 9", (string)$container['values']);
    
    $last = $assoc->getLast();
    $this->assertEquals('', (string)$last['id']);
    $this->assertEquals('Value 9', (string)$last['values']);
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testSearch($container) {
    $assoc = $container->fetchAssoc(array('values'), 'id');
    
    $this->assertEquals('300', $assoc->search('value 3', 'values'));
    $this->assertFalse($assoc->search('value 10', 'values'));
    
    $assoc = $container->fetchAssoc('values', 'id');
    
    $this->assertEquals(3, $assoc->search('value 3', 'values'));
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testDelOnInterrate($container) {
    $assoc = $container->fetchAssoc(array('values'), 'id');
    $check_next = FALSE;
    
    foreach ($assoc as $key => $value) {
      if ($check_next) {
        $this->assertEquals(400, $key);
        $this->assertEquals('value 4', (string)$value['values']);
        $check_next = FALSE;
      }
      if ($key == 300) {
        unset($assoc[$key]);
        $this->assertFalse(isset($assoc[$key]));
        $this->assertEquals("100\xfe200\xfe400\xfe500\xfe501\xfe502\xfe800", (string)$container['id']);
        $this->assertEquals("value 1\xfevalue 2\xfevalue 4\xfevalue 5\xfeValue 6\xfeValue 7\xfeValue 8\xfeValue 9", (string)$container['values']);
        $check_next = TRUE;
      }
      
      if ($key == $assoc->getLastKey()) {
        unset($assoc[$key]);
        $this->assertFalse(isset($assoc[$key]));
        $this->assertEquals("100\xfe200\xfe400\xfe500\xfe501\xfe502", (string)$container['id']);
        $this->assertEquals("value 1\xfevalue 2\xfevalue 4\xfevalue 5\xfeValue 6\xfeValue 7\xfeValue 9", (string)$container['values']);
      }
    }
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testDelNonExist($container) {
    $assoc = $container->fetchAssoc(array('values'), 'id');
    
    unset($assoc[555]);
    $this->assertEquals("100\xfe200\xfe400\xfe500\xfe501\xfe502", (string)$container['id']);
    $this->assertEquals("value 1\xfevalue 2\xfevalue 4\xfevalue 5\xfeValue 6\xfeValue 7\xfeValue 9", (string)$container['values']);
  }
  
  /**
   * @depends testuArrayContainer
   */
  public function testIsset($container) {
    $assoc = $container->fetchAssoc('values', 'id');
    
    $this->assertTrue(isset($assoc[3]));
    
    $this->assertFalse(isset($assoc[9999]));  
  }
}