<?php

namespace Drupal\Tests\drupalmoduleupgrader\Unit\Plugin\DMU\Converter\Functions;

use Pharborist\Parser;

/**
 * @group DMU.Converter.Functions
 * @covers \Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions\DbDelete
 */
class DbDeleteTest extends FunctionCallModifierTestBase {

  public function testRewriteQuery() {
    $function_call = Parser::parseExpression('db_delete("foo")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::database()->delete("foo")', $rewritten->getText());
  }

  public function testSystemQuery() {
    $function_call = Parser::parseExpression('db_delete("system")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertEquals(NULL, $rewritten);
  }

}
