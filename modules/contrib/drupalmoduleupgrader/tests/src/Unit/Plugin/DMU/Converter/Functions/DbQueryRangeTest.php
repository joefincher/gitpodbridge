<?php

namespace Drupal\Tests\drupalmoduleupgrader\Unit\Plugin\DMU\Converter\Functions;

use Pharborist\Parser;

/**
 * @group DMU.Converter.Functions
 * @covers \Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions\DbQueryRange
 */
class DbQueryRangeTest extends FunctionCallModifierTestBase {

  public function testRewriteOnlyQuery() {
    $function_call = Parser::parseExpression('db_query_range("SELECT nid FROM {node}", 0, 50)');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::database()->queryRange("SELECT nid FROM {node}", 0, 50)', $rewritten->getText());
  }

  public function testRewriteArgumentQuery() {
    $function_call = Parser::parseExpression('db_query_range("SELECT nid FROM {node} WHERE nid > :nid", 0, 50, [":nid" => 50])');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::database()->queryRange("SELECT nid FROM {node} WHERE nid > :nid", 0, 50, [":nid" => 50])', $rewritten->getText());
  }

}
