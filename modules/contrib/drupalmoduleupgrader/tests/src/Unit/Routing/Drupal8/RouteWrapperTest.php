<?php

namespace Drupal\Tests\drupalmoduleupgrader\Unit\Routing\Drupal8;

use Drupal\drupalmoduleupgrader\Routing\Drupal8\RouteWrapper;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Routing\Route;

/**
 * @group DMU.Routing
 */
class RouteWrapperTest extends UnitTestCase {

  private $route, $wrapper;

  public function setUp() {
    parent::setUp();
    $this->route = new Route('user/{user}/edit');
    $this->wrapper = new RouteWrapper('user.edit', $this->route, $this->createMock('\Drupal\Core\Routing\RouteProviderInterface'));
  }

  public function testGetIdentifier() {
    $this->assertEquals('user.edit', $this->wrapper->getIdentifier());
  }

  public function testGetPath() {
    $this->assertInstanceOf('\Drupal\drupalmoduleupgrader\Utility\Path\Drupal8\PathUtility', $this->wrapper->getPath());
    $this->assertEquals('/user/{user}/edit', $this->wrapper->getPath());
  }

}
