<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Converter\Functions;

use Drupal\drupalmoduleupgrader\TargetInterface;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\Objects\ClassMethodCallNode;

/**
 * @Converter(
 *  id = "db_update",
 *  description = @Translation("Rewrites calls to db_update()."),
 * )
 */
class DbUpdate extends FunctionCallModifier {

  /**
   * Tables which will cause the function call to be commented out.
   *
   * @var string[]
   */
  protected $forbiddenTables = ['system', 'variable'];

  /**
   * {@inheritdoc}
   */
  public function rewrite(FunctionCallNode $call, TargetInterface $target) {
    $arguments = $call->getArguments();
    $table = trim(trim((string) $arguments[0], "'"), '"');

    if (!in_array($table, $this->forbiddenTables)) {
      $object = ClassMethodCallNode::create('\Drupal', 'database')
        ->appendMethodCall('update')
        ->appendArgument(clone $arguments[0]);
      if (!empty($arguments[1])) {
        $object->appendArgument(clone $arguments[1]);
      }

      return $object;
    }
  }

}
