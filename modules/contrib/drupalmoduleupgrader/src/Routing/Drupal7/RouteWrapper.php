<?php

namespace Drupal\drupalmoduleupgrader\Routing\Drupal7;

use Doctrine\Common\Collections\ArrayCollection;
use Drupal\drupalmoduleupgrader\Routing\RouterBuiltEvent;
use Drupal\drupalmoduleupgrader\Routing\RouteWrapperInterface;
use Drupal\drupalmoduleupgrader\Utility\Path\Drupal7\PathUtility;

/**
 * Encapsulates a Drupal 7 route (including the link, if any).
 */
class RouteWrapper extends ArrayCollection implements RouteWrapperInterface {

  /**
   * @var \Drupal\drupalmoduleupgrader\Utility\Path\Drupal7\PathUtility
   */
  protected $path;

  /**
   * @var \Drupal\drupalmoduleupgrader\Routing\RouterInterface
   */
  protected $router;

  /**
   * @var static|null
   */
  protected $parent;

  /**
   * @var \Drupal\drupalmoduleupgrader\Routing\Drupal7\Router
   */
  protected $children;

  /**
   * @var \Drupal\drupalmoduleupgrader\Routing\Drupal7\Router
   */
  protected $siblings;

  /**
   * Constructs a Route object.
   */
  public function __construct($path, array $item) {
    $this->path = new PathUtility($path);

    // Merge in hook_menu() defaults to normalize things.
    $item += [
      'title callback' => 't',
      'title arguments' => [],
      'access callback' => 'user_access',
      'access arguments' => [],
      'page arguments' => [],
      'type' => 'MENU_NORMAL_ITEM',
    ];
    parent::__construct($item);
  }

  /**
   * {@inheritdoc}
   */
  public function getIdentifier() {
    return $this->getPath()->__toString();
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * {@inheritdoc}
   */
  public function hasParent() {
    return isset($this->parent);
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function unwrap() {
    return $this->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function onRouterBuilt(RouterBuiltEvent $event) {
    $this->router = $event->getRouter();

    $my_path = $this->getPath();
    $my_length = sizeof($my_path);
    $my_path = (string) $my_path;

    // If trying to get the parent raises an exception, we're going to
    // bail out. But we don't need the parent in order to find our own
    // children, so search for them before searching for the parent.
    $this->children = $this->router
      ->filter(function (RouteWrapper $route) use ($my_path, $my_length) {
        $path = $route->getPath();
        // <WTF>$path needs to be explicitly cast to a string, 'cause strPos() won't do
        // it, even though trim() and similar functions will.</WTF>
        return (sizeof($path) == ($my_length + 1) && strpos((string) $path, $my_path) === 0);
      })
      ->ofType('MENU_LOCAL_TASK, MENU_DEFAULT_LOCAL_TASK, MENU_LOCAL_ACTION');

    try {
      $parent = $this->getPath()->getParent();
      $this->parent = $this->router->get($parent->__toString());
    }
    catch (\LengthException $e) {
      // Because there's no parent path, we can't effectively search for siblings.
      // Time to die.
      return;
    }

    $this->siblings = $this->router
      ->filter(function (RouteWrapper $route) use ($parent, $my_path, $my_length) {
        $path = $route->getPath();
        // <WTF>strPos(), <sarcasm>in its wisdom</sarcasm>, won't cast to string.</WTF>
        return ($path !== $my_path && sizeof($path) == $my_length && strpos((string) $path, (string) $parent) === 0);
      });
  }

  /**
   * Returns if this route has an absolute access flag (TRUE or FALSE).
   *
   * @return bool
   */
  public function isAbsoluteAccess() {
    return is_bool($this->get('access callback'));
  }

  /**
   * Returns if this route has permission-based access.
   *
   * @return bool
   */
  public function isPermissionBased() {
    return ($this->get('access callback') == 'user_access');
  }

  /**
   * Returns if this route exposes a link of any kind.
   *
   * @return bool
   */
  public function hasLink() {
    return ($this->isLink() || $this->isLocalTask() || $this->isDefaultLocalTask() || $this->isLocalAction());
  }

  /**
   * Returns if this route is a normal link.
   *
   * @return bool
   */
  public function isLink() {
    return $this->get('type') == 'MENU_NORMAL_ITEM';
  }

  /**
   * Returns if this route is a local task (NOT a default local task).
   *
   * @return bool
   */
  public function isLocalTask() {
    return $this->get('type') == 'MENU_LOCAL_TASK';
  }

  /**
   * Gets the closest default local task, if there is one.
   *
   * @return static|null
   */
  public function getDefaultTask() {
    if ($this->hasSiblings()) {
      return $this->getSiblings()->ofType('MENU_DEFAULT_LOCAL_TASK')->first();
    }
  }

  /**
   * Returns if this route is a default local task.
   *
   * @return bool
   */
  public function isDefaultLocalTask() {
    return $this->get('type') == 'MENU_DEFAULT_LOCAL_TASK';
  }

  /**
   * Returns if this route is a local action.
   *
   * @return bool
   */
  public function isLocalAction() {
    return $this->get('type') == 'MENU_LOCAL_ACTION';
  }

  /**
   * Returns if this route is a contextual link.
   *
   * @return bool
   */
  public function isContextualLink() {
    return ($this->isLocalAction() && $this->containsKey('context') && $this->get('context') == 'MENU_CONTEXT_INLINE');
  }

  /**
   * Returns if this route has children.
   *
   * @return bool
   */
  public function hasChildren() {
    return $this->getChildren()->count() > 0;
  }

  /**
   * Returns the immediate children of this route.
   *
   * @return \Drupal\drupalmoduleupgrader\Routing\Drupal7\Router
   */
  public function getChildren() {
    return $this->children;
  }

  /**
   * Returns if this route has siblings.
   *
   * @return bool
   */
  public function hasSiblings() {
    return (bool) $this->getSiblings() > 0;
  }

  /**
   * Gets the siblings of this route.
   *
   * @return \Drupal\drupalmoduleupgrader\Routing\Drupal7\Router
   */
  public function getSiblings() {
    return $this->siblings;
  }

}
