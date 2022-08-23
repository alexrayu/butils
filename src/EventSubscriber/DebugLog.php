<?php

namespace Drupal\butils\EventSubscriber;

use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Class DebugLog: write the cumulative log on core shutdown.
 */
class DebugLog implements EventSubscriberInterface {

  /**
   * Debug logger..
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;

  /**
   * Constructs a new DebugLog object.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger channel factory.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_factory) {
    $this->logger = $logger_factory->get('debug');
  }

  /**
   * Writes the debug log if any.
   *
   * @param \Symfony\Component\HttpKernel\Event\TerminateEvent $event
   *   The Event to process.
   */
  public function onTerminate(TerminateEvent $event) {
    $log =& drupal_static('butils_debug_log', []);
    if (!empty($log)) {
      $flat = '';
      foreach ($log as $channel => $records) {
        $flat .= "\n\r";
        $flat .= "CHANNEL: $channel<br />\n\r";
        $prev_time = 0;
        foreach ($records as $key => $record) {
          $number = $key + 1;
          $message = $record['message'];
          $time = (string) $prev_time ? round($record['micros'] - $prev_time, 2) : 0;
          $flat .= '[' . $number . '] ' . $time . 'μs' . ": $message<br />\n\r";
          $prev_time = $record['micros'];
        }
      }
      $this->logger->debug($flat);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::TERMINATE][] = ['onTerminate', 100];
    return $events;
  }

}
