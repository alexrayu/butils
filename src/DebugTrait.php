<?php

namespace Drupal\butils;

/**
 * Trait DebugTrait.
 *
 * Debugging utils.
 */
trait DebugTrait {

  /**
   * Writes a record to debug log.
   *
   * @param string $channel
   *   Channel id.
   * @param string $message
   *   Message.
   */
  public function debugLog($channel, $message) {
    $log =& drupal_static('butils_debug_log', []);
    $log[$channel] = $log[$channel] ?? [];
    $log[$channel][] = [
      'micros' => microtime(TRUE),
      'message' => $message,
    ];
  }

}
