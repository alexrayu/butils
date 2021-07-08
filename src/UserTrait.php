<?php

namespace Drupal\butils;

use Drupal\Core\Session\AccountInterface;

/**
 * Trait User.
 *
 * User related utils.
 */
trait UserTrait {

  /**
   * Check if user access given specified roles.
   *
   * @param int|\Drupal\Core\Session\AccountInterface $account
   *   User id or user account.
   * @param array $rids
   *   Role ids.
   *
   * @return bool
   *   Check result.
   */
  public function userAccessRoles($account, array $rids = []) {
    if (is_numeric($account)) {
      $account = $this->entityTypeManager->getStorage('user')->load($account);
    }
    if (!$account instanceof AccountInterface) {
      return FALSE;
    }
    if ($account->id() === 1) {
      return TRUE;
    }
    $roles = $account->getRoles();
    if (in_array('administrator', $roles)) {
      return TRUE;
    }

    return !empty(array_intersect($roles, $rids));
  }

  /**
   * Check whether user is an admin.
   *
   * @param int|\Drupal\Core\Session\AccountInterface $account
   *   User id or user account.
   *
   * @return bool
   *   Check result.
   */
  public function isAdmin($account = NULL) {
    if (!$account) {
      $account = $this->currentUser;
    }
    return $this->userAccessRoles($account, ['administrator']);
  }

  /**
   * Gets user profile of a type by user id.
   *
   * @param int $uid
   *   User uid.
   * @param string $type
   *   Profile type.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Profile if any.
   */
  public function getProfile($uid, $type) {
    if (!$this->moduleHandler->moduleExists('profile')) {
      return NULL;
    }
    $list = \Drupal::entityTypeManager()
      ->getStorage('profile')
      ->loadByProperties([
        'uid' => $uid,
        'type' => $type,
      ]);
    $profile = NULL;
    if (!empty($list)) {
      $profile = reset($list);
    }

    return $profile;
  }

}
