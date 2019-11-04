<?php

namespace Drupal\butils;

/**
 * Trait State.
 *
 * Html related utils.
 */
trait StateTrait {

  /**
   * Get the key from the state.
   *
   * @param string $key
   *   State variable key.
   *
   * @return mixed
   *   Key value.
   */
  public function getState($key) {
    return $this->state->get($key);
  }

  /**
   * Sets the state key to a value.
   *
   * @param string $key
   *   State variable key.
   * @param mixed $value
   *   Key value.
   *
   * @return bool
   *   Result operation.
   */
  public function setState($key, $value) {
    return $this->state->set($key, $value);
  }

}
