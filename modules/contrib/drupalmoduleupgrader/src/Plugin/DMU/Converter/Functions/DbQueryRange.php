<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions;

use Drupal\drupalmoduleupgrader\TargetInterface;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\Objects\ClassMethodCallNode;

/**
 * @Converter(
 *  id = "db_query_range",
 *  description = @Translation("Rewrites calls to db_query_range()."),
 * )
 */
class DbQueryRange extends FunctionCallModifier {

  /**
   * {@inheritdoc}
   */
  public function rewrite(FunctionCallNode $call, TargetInterface $target) {
    $arguments = $call->getArguments();

    $object = ClassMethodCallNode::create('\Drupal', 'database')
      ->appendMethodCall('queryRange')
      ->appendArgument(clone $arguments[0])
      ->appendArgument(clone $arguments[1])
      ->appendArgument(clone $arguments[2]);
    if (!empty($arguments[3])) {
      $object->appendArgument(clone $arguments[3]);
    }
    if (!empty($arguments[4])) {
      $object->appendArgument(clone $arguments[4]);
    }

    return $object;
  }

}
