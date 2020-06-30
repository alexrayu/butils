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

}
