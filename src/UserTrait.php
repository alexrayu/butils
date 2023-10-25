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
    $list = $this->entityTypeManager
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

  /**
   * Compose a username for a user.
   *
   * @param string $first_name
   *   First name.
   * @param string $last_name
   *   Last name.
   *
   * @return string
   *   Generated user name.
   */
  public function composeUsername($first_name, $last_name) {
    $first_name = substr($first_name, 0, 10);
    $first_name = preg_replace('/[^A-Za-z0-9 ]/', '', $first_name);
    $last_name = substr($last_name, 0, 10);
    $last_name = preg_replace('/[^A-Za-z0-9 ]/', '', $last_name);
    $username = strtolower($first_name . '.' . $last_name);

    // Username does not exists outside this account, use it.
    $uids = $this->entityTypeManager->getStorage('user')->getQuery()
      ->condition('name', $username)
      ->accessCheck(FALSE)
      ->execute();
    if (empty($uids)) {
      return $username;
    }

    // Validation failed, check the latest version of the username.
    $counter = 1;
    do {
      $computed_username = $username . '.' . $counter++;
    } while (
      (bool) $this->database->select('users_field_data')
        ->condition('name', $computed_username)
        ->countQuery()
        ->execute()
        ->fetchField()
    );

    return $computed_username;
  }

}
