<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Converter;

use Drupal\Core\Database\Query\Condition;
use Drupal\drupalmoduleupgrader\ConverterBase;
use Drupal\drupalmoduleupgrader\TargetInterface;

/**
 * @Converter(
 *  id = "hook_form_alter",
 *  description = @Translation("Corrects hook_form_alter() function signatures.")
 * )
 */
class HookFormAlter extends ConverterBase {

  /**
   * {@inheritdoc}
   */
  public function convert(TargetInterface $target) {
    $indexer = $target->getIndexer('function');

    $query = $indexer->getQuery();
    $db_or = new Condition('OR');
    $db_or->condition('id', $target->id() . '_form_alter');
    $db_or->condition('id', $query->escapeLike($target->id() . '_form_') . '%' . $query->escapeLike('_alter'), 'LIKE');
    $query->condition($db_or);
    $alter_hooks = $query->execute();

    foreach ($alter_hooks as $alter_hook) {
      /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
      $function = $indexer->get($alter_hook->id);

      $parameters = $function->getParameters();
      if (sizeof($parameters) > 1) {
        $parameters[1]->setTypeHint('\Drupal\Core\Form\FormStateInterface');
        $target->save($function);
      }
    }
  }

}
