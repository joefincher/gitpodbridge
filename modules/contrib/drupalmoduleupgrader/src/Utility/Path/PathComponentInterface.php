<?php

namespace Drupal\drupalmoduleupgrader\Utility\Path;

/**
 * Represents a single component of a route path.
 */
interface PathComponentInterface {

  /**
   * Constructs the path component.
   *
   * @param mixed $value
   */
  public function __construct($value);

  /**
   * @return string
   */
  public function __toString();

  /**
   * Returns if this component is considered a wildcard.
   *
   * @return bool
   */
  public function isWildcard();

}
