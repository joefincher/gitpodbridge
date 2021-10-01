<?php

namespace Drupal\drupalmoduleupgrader\Plugin\DMU\Fixer;

/**
 * Trait used by fixers which loop through existing indexer objects and do
 * things with them.
 */
trait NodeCollectorTrait {

  protected function getObjects() {
    /** @var \Pharborist\NodeCollection $objects */
    $objects = $this->target->getIndexer($this->configuration['type'])->get($this->configuration['id']);

    if (isset($this->configuration['where'])) {
      $where = $this->configuration['where'];
      // If the first character of the filter is an exclamation point, negate it.
      return ($where[0] == '!' ? $objects->not(substr($where, 1)) : $objects->filter($where));
    }
    else {
      return $objects;
    }
  }

}
