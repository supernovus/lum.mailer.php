<?php 

namespace Lum\Mailer\Templates;

use Lum\Mailer\{Message};

/**
 * Use PHP templates.
 * 
 * Will use a lum-core view loader if the `views` option is set (recommended).
 * 
 * Otherwise it will expect the template names to be full file paths,
 * and will use `Lum\Core::get_php_content()` to get the rendered message.
 */
class ViewLoader extends Plugin 
{
  public function renderMessage(Message $msg) 
  {
    $data = $msg->getData();
    $core = \Lum\Core::getInstance();
    $loader = $this->opts['views'] ?? null;

    if (isset($loader, $core->$loader))
    { // We're using a view loader.
      if (isset($msg->htmlTemplate))
      {
        $msg->htmlMessage = $core->$loader->load($msg->htmlTemplate, $data);
      }
      if (isset($msg->textTemplate))
      {
        $msg->textMessage = $core->$loader->load($msg->textTemplate, $data);
      }
    }
    else
    { // View library wasn't found. Assuming a full PHP include file path.
      if (isset($msg->htmlTemplate))
      {
        $msg->htmlMessage = \Lum\Core::get_php_content($msg->htmlTemplate, $data);
      }
      if (isset($msg->textTemplate))
      {
        $msg->textMessage = \Lum\Core::get_php_content($msg->textTemplate, $data);
      }
    }
  }

}
