<?php

namespace Lum\Mailer\Templates;

use Lum\Mailer\{BasePlugin,Message};

/**
 * A common API for Template engine plugins.
 */
abstract class Plugin extends BasePlugin
{
  abstract public function renderMessage(Message $message);

  public function setupMessage(Message $message)
  {
    return;
  }
}
