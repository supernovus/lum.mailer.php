<?php

namespace Lum\Mailer;

/**
 * An underlying handler interface for the Lum\Mailer\Framework class.
 */
interface Handler
{
  function send_message ($message, $opts=[]);
}