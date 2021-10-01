<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions;

use Drupal\drupalmoduleupgrader\TargetInterface;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\Objects\ClassMethodCallNode;

/**
 * @Converter(
 *  id = "drupal_get_messages",
 *  description = @Translation("Rewrites calls to drupal_get_messages()."),
 * )
 */
class DrupalMessageGet extends FunctionCallModifier {

  /**
   * {@inheritdoc}
   */
  public function rewrite(FunctionCallNode $call, TargetInterface $target) {
    $arguments = $call->getArguments();

    if (isset($arguments[0]) && !empty($arguments[0]) && strtoupper($arguments[0]) != 'NULL') {
      if (isset($arguments[1]) && !empty($arguments[0]) && strtoupper($arguments[1]) != 'NULL') {
        return ClassMethodCallNode::create('\Drupal', 'messenger')
          ->appendMethodCall('messagesByType')
          ->appendArgument(clone $arguments[0]);
      }
      else {
        return ClassMethodCallNode::create('\Drupal', 'messenger')
          ->appendMethodCall('deleteByType')
          ->appendArgument(clone $arguments[0]);
      }
    }
    else {
      if (isset($arguments[1]) && !empty($arguments[0]) && strtoupper($arguments[1]) != 'NULL') {
        return ClassMethodCallNode::create('\Drupal', 'messenger')
          ->appendMethodCall('all');
      }
      else {
        return ClassMethodCallNode::create('\Drupal', 'messenger')
          ->appendMethodCall('deleteAll');
      }
    }
  }

}
