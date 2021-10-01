<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions;

use Drupal\drupalmoduleupgrader\TargetInterface;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\Objects\ClassMethodCallNode;

/**
 * @Converter(
 *  id = "drupal_set_message",
 *  description = @Translation("Rewrites calls to drupal_set_message()."),
 * )
 */
class DrupalMessageSet extends FunctionCallModifier {

  /**
   * Array to generate services for drupal_set_messages.
   *
   * @var arraymessengers
   */
  protected $messengers = [
    'custom' => 'addMessage',
    'error' => 'addError',
    'status' => 'addStatus',
    'warning' => 'addWarning',
  ];

  /**
   * {@inheritdoc}
   */
  public function rewrite(FunctionCallNode $call, TargetInterface $target) {
    $arguments = $call->getArguments();
    if (isset($arguments[0]) && !empty($arguments[0])) {
      if (isset($arguments[1]) && !empty($arguments[1])) {
        $arguments_1 = trim(trim((string) $arguments[1], "'"), '"');
        $object = ClassMethodCallNode::create('\Drupal', 'messenger')
          ->appendMethodCall($this->messengers[$arguments_1])
          ->appendArgument(clone $arguments[0]);
        if ($arguments_1 == 'custom') {
          $object->appendArgument(clone $arguments[1]);
        }
        return $object;
      }
      return ClassMethodCallNode::create('\Drupal', 'messenger')
        ->appendMethodCall('addMessage')
        ->appendArgument(clone $arguments[0]);
    }
    return ClassMethodCallNode::create('\Drupal', 'messenger')
      ->appendMethodCall('all');
  }

}
