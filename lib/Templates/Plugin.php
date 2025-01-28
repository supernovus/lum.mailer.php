<?php

namespace Lum\Mailer\Templates;

use Lum\Mailer\{BasePlugin,Message};

/**
 * A common API for Template engine plugins.
 */
abstract class Plugin extends BasePlugin
{
  abstract public function renderMessage(Message $msg);

  /**
   * Common setup for template plugins handles required fields.
   */
  public function setupMessage(Message $msg)
  {
    $fields = $this->manager->getKnownFields();

    if (!isset($fields))
    { // No fields to test, nothing more to do here.
      return;
    }

    $data = $msg->getData();

    foreach ($fields as $field => $required)
    {
      if ($required && empty($data[$field]))
      {
        $msg->missing[$field] = true;
      }
    }

    // We can only continue if all required fields are present.
    if (count($msg->missing))
    { // We have missing values.
      $msg->valid = false;
      if ($this->manager->log_errors)
      {
        $log = ["data"=>$data, "missing"=>$msg->missing];
        error_log("«Lum-Mailer::Templates» ".json_encode($data));
      }
    }
  }
}
