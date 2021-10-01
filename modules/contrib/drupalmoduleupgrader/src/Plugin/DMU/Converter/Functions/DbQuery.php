<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions;

use Drupal\drupalmoduleupgrader\TargetInterface;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\Objects\ClassMethodCallNode;

/**
 * @Converter(
 *  id = "db_query",
 *  description = @Translation("Rewrites calls to db_query()."),
 * )
 */
class DbQuery extends FunctionCallModifier {

  /**
   * {@inheritdoc}
   */
  public function rewrite(FunctionCallNode $call, TargetInterface $target) {
    $arguments = $call->getArguments();

    $object = ClassMethodCallNode::create('\Drupal', 'database')
      ->appendMethodCall('query')
      ->appendArgument(clone $arguments[0]);
    if (!empty($arguments[1])) {
      $object->appendArgument(clone $arguments[1]);
    }
    if (!empty($arguments[2])) {
      $object->appendArgument(clone $arguments[0]);
    }

    return $object;
  }

}
