<?php

namespace Drupal\Tests\drupalmoduleupgrader\Unit\Plugin\DMU\Converter\Functions;

use Pharborist\Parser;

/**
 * @group DMU.Converter.Functions
 * @covers \Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions\DrupalMessageGet
 */
class DrupalGetMessageTest extends FunctionCallModifierTestBase {

  /**
   * Test conversion of drupal_get_messages().
   */
  public function testRetriveRemoveAllMessage() {
    $function_call = Parser::parseExpression('drupal_get_messages()');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->deleteAll()', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_get_messages("error").
   */
  public function testRetriveRemoveSpecificTypeMessage() {
    $function_call = Parser::parseExpression('drupal_get_messages("error")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->deleteByType("error")', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_get_messages("error", FALSE).
   */
  public function testOnlyRetriveSpecificTypeMessage() {
    $function_call = Parser::parseExpression('drupal_get_messages("error", FALSE)');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->messagesByType("error")', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_get_messages(NULL, FALSE).
   */
  public function testOnlyRetriveAllMessage() {
    $function_call = Parser::parseExpression('drupal_get_messages(NULL, FALSE)');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->all()', $rewritten->getText());
  }

}
