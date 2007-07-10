<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbObjectTest.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require('limb/classkit/src/lmbObject.class.php');

class ObjectTestVersion extends lmbObject
{
  public $bar;
  public $_guarded = 'yeah';
  protected $protected = 'me';

  function getBar()
  {
    return $this->bar . '_get_called';
  }

  function setBar($value)
  {
    $this->bar = $value . '_set_called';
  }
}

class lmbObjectTest extends UnitTestCase
{
  function testHasAttribute()
  {
    $object = new lmbObject();
    $object->set('bar', 1);

    $this->assertFalse($object->hasAttribute('foo'));
    $this->assertTrue($object->hasAttribute('bar'));
  }

  function testHasAttributeForExistingButNullProperty()
  {
    $object = new lmbObject();
    $object->set('foo', null);
    $this->assertTrue($object->hasAttribute('foo'));
  }

  function testHasAttributeForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $this->assertFalse($object->hasAttribute('_guarded'));
  }

  function testGetAttributesNames()
  {
    $object = new ObjectTestVersion();
    $this->assertEqual($object->getAttributesNames(), array('bar', 'protected'));
  }

  function testGetNull()
  {
    $object = new lmbObject();
    $this->assertNull($object->get('foo'));
  }

  function testSetGet()
  {
    $object = new lmbObject();
    $object->set('foo', 1);

    $this->assertEqual($object->get('foo'), 1);
  }

  function testCallGetterForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $this->assertNull($object->get('_guarded'));
  }

  function testNonExistingGetter()
  {
    $object = new lmbObject();
    $object->set('foo_bar_yo', 1);

    $this->assertEqual($object->getFooBarYo(), 1);
  }

  function testNonExistingSetter()
  {
    $object = new lmbObject();
    $object->setFooBarYo(1);

    $this->assertEqual($object->getFooBarYo(), 1);
  }

  function testCallGetterForPropertyIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->bar = 'BAR';
    $this->assertEqual($object->get('bar'), 'BAR_get_called');
  }

  function testCallSetterForPropertyIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->set('bar', 'BAR');
    $this->assertEqual($object->bar, 'BAR_set_called');
  }

  function testImportMergesWithExistingProps()
  {
    $object = new lmbObject();
    $object->set('foo', 'hey');
    $object->set('baz', 'wow');
    $object->import(array('foo' => 'test', 'bar' => 'test2'));

    $this->assertEqual($object->get('foo'), 'test');
    $this->assertEqual($object->get('bar'), 'test2');
    $this->assertEqual($object->get('baz'), 'wow');
  }

  function testImportIgnoresGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->import(array('_guarded' => 'no'));
    $this->assertEqual($object->_guarded, 'yeah');
  }

  function testPassAttributesInConstructor()
  {
    $object = new lmbObject(array('foo' => 'hey', 'baz' => 'wow'));
    $this->assertEqual($object->get('foo'), 'hey');
    $this->assertEqual($object->get('baz'), 'wow');
  }

  function testExport()
  {
    $object = new lmbObject();
    $object->set('foo', 'yo-yo');
    $object->set('bar', 'zoo');

    $this->assertEqual($object->export(), array('foo' => 'yo-yo', 'bar' => 'zoo'));
  }

  function testExportOnlyNonGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->set('foo', 'FOO');

    $this->assertEqual($object->export(), array('bar' => null, 'foo' => 'FOO', 'protected' => 'me'));
  }

  function testRemove()
  {
    $object = new lmbObject();
    $object->set('bar', 1);
    $object->set('foo', 2);

    $object->remove('bar');

    $this->assertEqual($object->get('foo'), 2);
    $this->assertNull($object->get('bar'));
    $this->assertFalse($object->hasAttribute('bar'));
  }

  function testRemoveForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $object->remove('_guarded');

    $this->assertEqual($object->_guarded, 'yeah');
  }

  function testRemoveAll()
  {
    $object = new lmbObject();
    $object->set('bar', 1);
    $object->set('foo', 2);

    $object->removeAll();

    $this->assertEqual($object->export(), array());
  }

  function testRemoveAllExceptGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->removeAll();
    $this->assertEqual($object->_guarded, 'yeah');
  }

  function testGetHash()
  {
    $o1 = new lmbObject();
    $o2 = new lmbObject();

    $this->assertNotNull($o1->getHash());
    $this->assertEqual($o1->getHash(), $o2->getHash());
  }

  function testGetClass()
  {
    $o1 = new lmbObject();
    $this->assertEqual($o1->getClass(), 'lmbObject');

    $o2 = new ObjectTestVersion($this);
    $this->assertEqual($o2->getClass(), 'ObjectTestVersion');
  }

  function testGetByPathWithArrays()
  {
    $o = new lmbObject(array('foo' => array('bar' => array('zoo' => 2))));
    $this->assertEqual($o->getByPath('foo.bar.zoo'), 2);
  }

  function testGetByPathWithObjects()
  {
    $o = new lmbObject(array('foo' => new lmbObject(array('bar' => new lmbObject(array('zoo' => 2))))));
    $this->assertEqual($o->getByPath('foo.bar.zoo'), 2);
  }

  function testGetByPathForNonExistingItemWithArrays()
  {
    $o = new lmbObject(array('foo' => array('bar' => array('zoo' => 2))));
    $this->assertNull($o->getByPath('foo.such.prop'));
  }

  function testGetByPathForNonExistingItemWithObjects()
  {
    $o = new lmbObject(array('foo' => new lmbObject(array('bar' => new lmbObject(array('zoo' => 2))))));
    $this->assertNull($o->getByPath('foo.such.prop'));
  }

  function testImplementsArrayAccessInterface()
  {
    $o = new lmbObject();

    $o->set('foo', 'Bar');
    $this->assertEqual($o['foo'], 'Bar');

    $o['foo'] = 'Zoo';
    $this->assertEqual($o->get('foo'), 'Zoo');

    unset($o['foo']);
    $this->assertNull($o->get('foo'));

    $o->set('foo', 'Bar');
    $this->assertTrue(isset($o['foo']));
    $this->assertFalse(isset($o['bar']));
  }
}
?>