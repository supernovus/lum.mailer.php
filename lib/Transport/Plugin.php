<?php

namespace Lum\Mailer\Transport;

use Lum\Mailer\{BasePlugin,Message};

/**
 * A common API for Transport plugins.
 */
abstract class Plugin extends BasePlugin
{
  abstract public function sendMessage (Message $message);

  public function setupMessage(Message $message)
  {
    return;
  }
}
