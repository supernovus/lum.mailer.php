<?php 

namespace Lum\Mailer\Templates;

use Lum\Mailer\Message;

/**
 * A simple template that sends fields as a plain text list.
 * No HTML support at all. Use a proper template engine for that.
 * 
 * This minimalist template engine has only three options:
 * 
 * - `listHeader` (default: `"---"`) Text header for list.
 * - `listFooter` (default: `"---"`) Text footer for list.
 * - `listPrefix` (default: `" "`)   Prefix for each list item.
 * 
 * Other than those, list items will look like:
 * 
 * ```
 *  field1Name: valueFromData
 *  field2Name: anotherValue
 * ```
 * 
 * This engine depends on the default stringification prototcols,
 * and isn't really recommended for anything other than testing.
 */
class TextList extends Plugin 
{
  public function renderMessage(Message $msg) 
  {
    $header = $this->opts['listHeader'] ?? '---';
    $footer = $this->opts['listFooter'] ?? '---';
    $prefix = $this->opts['listPrefix'] ?? ' ';

    $message = "$header\n";

    $data = $msg->getData();
    foreach ($data as $field => $value)
    {
      $message .= $prefix."$field: $value\n";
    }

    $message .= "$footer\n";
    $msg->textMessage = $message;
  }
}
