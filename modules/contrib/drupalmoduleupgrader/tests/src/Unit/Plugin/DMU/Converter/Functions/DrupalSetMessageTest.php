<?php

namespace Drupal\Tests\drupalmoduleupgrader\Unit\Plugin\DMU\Converter\Functions;

use Pharborist\Parser;

/**
 * @group DMU.Converter.Functions
 * @covers \Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions\DrupalMessageSet
 */
class DrupalSetMessageTest extends FunctionCallModifierTestBase {

  /**
   * Test conversion of drupal_set_message("foo").
   */
  public function testRewriteOnlyMessage() {
    $function_call = Parser::parseExpression('drupal_set_message("foo")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->addMessage("foo")', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_set_message("foo", "custom").
   */
  public function testRewriteCustomMessage() {
    $function_call = Parser::parseExpression('drupal_set_message("foo", "custom")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->addMessage("foo", "custom")', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_set_message("foo", "error").
   */
  public function testRewriteErrorMessage() {
    $function_call = Parser::parseExpression('drupal_set_message("foo", "error")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->addError("foo")', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_set_message("foo", "status").
   */
  public function testRewriteStatusMessage() {
    $function_call = Parser::parseExpression('drupal_set_message("foo", "status")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->addStatus("foo")', $rewritten->getText());
  }

  /**
   * Test conversion of drupal_set_message("foo", "warning").
   */
  public function testRewriteWarningMessage() {
    $function_call = Parser::parseExpression('drupal_set_message("foo", "warning")');
    $rewritten = $this->getPlugin()->rewrite($function_call, $this->target);
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $rewritten);
    $this->assertEquals('\Drupal::messenger()->addWarning("foo")', $rewritten->getText());
  }

}
