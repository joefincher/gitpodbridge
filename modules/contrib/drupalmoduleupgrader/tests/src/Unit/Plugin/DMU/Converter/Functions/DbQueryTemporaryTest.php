<?php

namespace Drupal\Tests\drupalmoduleupgrader\Unit\Plugin\DMU\Converter\Functions;

use Pharborist\Parser;

/**
 * @group DMU.Converter.Functions
 * @covers \Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions\DbQueryTemporary
 */
class DbQueryTemporaryTest extends FunctionCallModifierTestBase {

  public function testRewriteOnlyQuery() {
    $function_call = Parser::parseExpression('db_query_temporary("SELECT nid FROM {node}")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::database()->queryTemporary("SELECT nid FROM {node}")', $rewritten->getText());
  }

  public function testRewriteArgumentQuery() {
    $function_call = Parser::parseExpression('db_query_temporary("SELECT nid FROM {node} WHERE nid > :nid", [":nid" => 50])');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::database()->queryTemporary("SELECT nid FROM {node} WHERE nid > :nid", [":nid" => 50])', $rewritten->getText());
  }

}
