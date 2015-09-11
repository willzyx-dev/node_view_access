<?php

/**
 * @file
 * Contains \Drupal\node_view_access\Tests\NodeViewAccessTestAccess.
 */

namespace Drupal\node_view_access\Tests;

use Drupal\node\Tests\NodeTestBase;
use Drupal\user\RoleInterface;

/**
 * Tests basic functionality of the node_view_access module.
 *
 * @group node_view_access
 *
 * @ingroup node_view_access
 */
class NodeViewAccessAccessTest extends NodeTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('node_view_access');

  /**
   * A published node of type page.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $pageNode;

  /**
   * An unpublished node of type page.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $unpublishedPageNode;

  /**
   * A published node of type article.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $articleNode;

  /**
   * An unpublished node of type article.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $unpublishedArticleNode;

  /**
   * Set up test.
   */
  protected function setUp() {
    parent::setUp();

    // Rebuild node access as node_view_access hint to do after install.
    node_access_rebuild();

    // Clear permissions for authenticated users.
    $this->config('user.role.' . RoleInterface::AUTHENTICATED_ID)->set('permissions', array())->save();

    // Create users.
    $admin_user = $this->drupalCreateUser(array('bypass node access'));

    // Create some contents.
    $this->pageNode = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $admin_user->id(),
    ));
    $this->unpublishedPageNode = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $admin_user->id(),
      'status' => 0,
    ));
    $this->articleNode = $this->drupalCreateNode(array(
      'type' => 'article',
      'uid' => $admin_user->id(),
    ));
    $this->unpublishedArticleNode = $this->drupalCreateNode(array(
      'type' => 'article',
      'uid' => $admin_user->id(),
      'status' => 0,
    ));
  }

  /**
   * Runs basic tests for node_view_access function.
   */
  public function testNodeViewAccess() {

    // Ensures user with 'access content' can't access node if specific
    // 'view type content' is not assigned.
    $web_user = $this->drupalCreateUser(array('access content'));
    $this->assertNodeAccess(array('view' => FALSE), $this->pageNode, $web_user);
    $this->assertNodeAccess(array('view' => FALSE), $this->articleNode, $web_user);

    // Ensures user without 'access content' can't access node even if
    // 'view type content' is assigned.
    $no_access_user = $this->drupalCreateUser(array(
      'view article content',
      'view page content',
    ));
    $this->assertNodeAccess(array('view' => FALSE), $this->pageNode, $no_access_user);
    $this->assertNodeAccess(array('view' => FALSE), $this->articleNode, $no_access_user);

    // Ensures user with 'view page content' can see node of type page but
    // not node of type article.
    $page_user = $this->drupalCreateUser(array(
      'access content',
      'view page content',
    ));
    $this->assertNodeAccess(array('view' => TRUE), $this->pageNode, $page_user);
    $this->assertNodeAccess(array('view' => FALSE), $this->articleNode, $page_user);

    // Ensures user with 'view article content' can see node of type article
    // but not node of type page.
    $article_user = $this->drupalCreateUser(array(
      'access content',
      'view article content',
    ));
    $this->assertNodeAccess(array('view' => FALSE), $this->pageNode, $article_user);
    $this->assertNodeAccess(array('view' => TRUE), $this->articleNode, $article_user);

  }

  /**
   * Runs basic tests for 'view own type content'.
   */
  public function testNodeViewAccessOwn() {
    // Ensures user with 'view own article content' can see own node of type
    // article even if 'view article content' is not granted but can't see
    // other article nodes.
    $own_article_user = $this->drupalCreateUser(array(
      'access content',
      'create article content',
      'view own article content',
    ));
    $own_article_node = $this->drupalCreateNode(array(
      'type' => 'article',
      'uid' => $own_article_user->id(),
    ));
    $this->assertNodeAccess(array('view' => FALSE), $this->articleNode, $own_article_user);
    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedArticleNode, $own_article_user);
    $this->assertNodeAccess(array('view' => TRUE), $own_article_node, $own_article_user);

    // Ensures user with 'view own page content' can see own node of type
    // page even if 'view page content' is not granted but can't see
    // other page nodes.
    $own_page_user = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'view own page content',
    ));
    $own_page_node = $this->drupalCreateNode(array(
      'type' => 'article',
      'uid' => $own_page_user->id(),
    ));
    $this->assertNodeAccess(array('view' => FALSE), $this->pageNode, $own_page_user);
    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedPageNode, $own_page_user);
    $this->assertNodeAccess(array('view' => TRUE), $own_page_node, $own_page_user);
  }

  /**
   * Runs basic tests 'view own type content' for unpublished node.
   */
  public function testNodeViewAccessOwnUnpublished() {
    // Ensures user with 'view own article content' but without
    // 'view own unpublished content' can see own node of type
    // article only if published.
    $own_article_user1 = $this->drupalCreateUser(array(
      'access content',
      'create article content',
      'view own article content',
    ));
    $unpublished_article_node1 = $this->drupalCreateNode(array(
      'type' => 'article',
      'uid' => $own_article_user1->id(),
      'status' => 0,
    ));

    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedArticleNode, $own_article_user1);
    $this->assertNodeAccess(array('view' => FALSE), $unpublished_article_node1, $own_article_user1);

    // Ensures user with 'view own article content' and
    // 'view own unpublished content' can see own node of type
    // article even if unpublished.
    $own_article_user2 = $this->drupalCreateUser(array(
      'access content',
      'create article content',
      'view own article content',
      'view own unpublished content',
    ));
    $unpublished_article_node2 = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $own_article_user2->id(),
      'status' => 0,
    ));

    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedArticleNode, $own_article_user2);
    $this->assertNodeAccess(array('view' => TRUE), $unpublished_article_node2, $own_article_user2);

    // Ensures user without 'view own article content' and
    // with 'view own unpublished content' can see own node of type
    // article even if unpublished.
    $own_article_user3 = $this->drupalCreateUser(array(
      'access content',
      'create article content',
      'view own unpublished content',
    ));
    $unpublished_article_node3 = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $own_article_user3->id(),
      'status' => 0,
    ));

    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedArticleNode, $own_article_user3);
    $this->assertNodeAccess(array('view' => TRUE), $unpublished_article_node3, $own_article_user3);

    // Ensures user with 'view own page content' but without
    // 'view own unpublished content' can see own node of type
    // page only if published.
    $own_page_user1 = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'view own page content',
    ));
    $unpublished_page_node1 = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $own_page_user1->id(),
      'status' => 0,
    ));

    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedPageNode, $own_page_user1);
    $this->assertNodeAccess(array('view' => FALSE), $unpublished_page_node1, $own_page_user1);

    // Ensures user with 'view own page content' and
    // 'view own unpublished content' can see own node of type
    // page even if unpublished.
    $own_page_user2 = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'view own page content',
      'view own unpublished content',
    ));
    $unpublished_page_node2 = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $own_page_user2->id(),
      'status' => 0,
    ));

    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedPageNode, $own_page_user2);
    $this->assertNodeAccess(array('view' => TRUE), $unpublished_page_node2, $own_page_user2);

    // Ensures user without 'view own page content' and with
    // 'view own unpublished content' can see own node of type
    // page even if unpublished.
    $own_page_user3 = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'view own unpublished content',
    ));
    $unpublished_page_node3 = $this->drupalCreateNode(array(
      'type' => 'page',
      'uid' => $own_page_user3->id(),
      'status' => 0,
    ));

    $this->assertNodeAccess(array('view' => FALSE), $this->unpublishedPageNode, $own_page_user3);
    $this->assertNodeAccess(array('view' => TRUE), $unpublished_page_node3, $own_page_user3);
  }

}
