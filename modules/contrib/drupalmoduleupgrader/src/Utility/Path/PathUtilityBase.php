<?php

namespace Drupal\drupalmoduleupgrader\Utility\Path;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Base class for PathUtilityInterface implementations.
 */
abstract class PathUtilityBase extends ArrayCollection implements PathUtilityInterface {

  /**
   * The next index for getNextWildcard() to slice on.
   *
   * @var int
   */
  protected $_wildcard = 0;

  /**
   * {@inheritdoc}
   */
  public function __construct($path) {
    if (is_array($path)) {
      foreach ($path as $component) {
        $this->add($component);
      }
    }
    elseif (is_string($path)) {
      $this->__construct(explode('/', $path));
    }
    else {
      throw new \InvalidArgumentException();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function add($value) {
    if ($value instanceof PathComponentInterface) {
      parent::add($value);
    }
    elseif (is_scalar($value)) {
      $this->add(static::getComponent($value));
    }
    else {
      throw new \InvalidArgumentException();
    }
  }

  /**
   * Filters the path by a string. The filtered path will only contain
   * components whose string representation is identical to $element.
   *
   * @param string $element
   *   The string to search for.
   *
   * @return static
   */
  public function find($element) {
    return $this
      ->filter(function (PathComponentInterface $component) use ($element) {
        return ($element === $component->__toString());
      });
  }

  /**
   * {@inheritdoc}
   */
  public function contains($element) {
    return (boolean) $this->find($element)->count();
  }

  /**
   * {@inheritdoc}
   */
  public function hasWildcards() {
    return ($this->getWildcards()->count() > 0);
  }

  /**
   * Returns every {wildcard} in the path, keyed by position.
   *
   * @return static
   */
  public function getWildcards() {
    return $this->filter(function (PathComponentInterface $component) {
      return $component->isWildcard();
    });
  }

  /**
   * Returns the next wildcard, if any.
   *
   * @return \Drupal\drupalmoduleupgrader\Utility\Path\PathComponentInterface|null
   */
  public function getNextWildcard() {
    $wildcards = $this->getWildcards()->slice($this->_wildcard, 1);

    if (isset($wildcards[$this->_wildcard])) {
      return $wildcards[$this->_wildcard++];
    }
  }

  /**
   * Returns a copy of the collection with wildcards removed.
   *
   * @return static
   */
  public function deleteWildcards() {
    return $this->filter(function (PathComponentInterface $component) {
      return (!$component->isWildcard());
    });
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    if ($this->count() > 1) {
      return new static($this->slice(0, -1));
    }
    else {
      throw new \LengthException('Cannot get parent a path with one component.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return implode('/', $this->toArray());
  }

  /**
   * After PHP 7.2+ for count() E_WARNING will now be emitted when attempting to count() non-countable types.
   * Ref: https://secure.php.net/manual/en/migration72.incompatible.php#migration72.incompatible.warn-on-non-countable-types
   *
   * Possible solutions will be to check is_countable() to check for variable is instanceof Countable, but
   * is_countable() is available from 7.3.
   *
   * To make compatible for lower version we need to suppress the warning for now because we know elements will
   * always be of array type.
   *
   * {@inheritdoc}
   */
  public function count() {
    return @count($this->toArray());
  }

}
