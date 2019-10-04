<?php

namespace Lum;

/**
 * A quick class to send e-mails with.
 * Use it as a standalone component, or extend it for additional features.
 *
 * It requires an underlying mailer plugin/handler.
 *
 * You can use 'Swift' to use SwiftMailer or 'SendGrid' to use SendGrid.
 * These are case sensitive since we're now using PSR-4 autoloading.
 *
 * They require the `swiftmailer/swiftmailer` or `sendgrid/sendgrid` packages
 * to be installed respectively.
 */
class Mailer
{
  // Internal rules.
  protected $fields;     // Field rules. 'true' required, 'false' optional.
  protected $views;      // Lum loader to use to load template.
  protected $handler;    // The underlying handler. Use get_handler()
  protected $def_template;  // Default template to use for e-mails.
  protected $alt_template;  // Alternative template to use for e-mails.

  // Public fields. Reset on each send().
  public $failures;     // A list of messages that failed.
  public $missing;      // Set to an array if a required field wasn't set.

  // Set to true to enable logging errors.
  public $log_errors = False;
  public $log_message = False;

  public function __construct ($fields=null, $opts=array())
  {
    // Send False or Null to disable the use of fields.
    if (is_array($fields))
      $this->fields = $fields;

    if (isset($opts['template']))
      $this->def_template = $opts['template'];

    if (isset($opts['alt_template']))
      $this->alt_template = $opts['alt_template'];

    if (isset($opts['views']))
      $this->views = $opts['views'];

    if (!isset($opts['handler']))
    { // Try to determine which handler to use.
      if (class_exists('\\Swift_Mailer'))
      {
        $opts['handler'] = "Swift";
      }
      elseif (class_exists('\\SendGrid'))
      {
        $opts['handler'] = "SendGrid";
      }
      else
      {
        throw new \Exception("Could not detect an underlying mail library, please install either SwiftMailer or SendGrid.");
      }
    }

    if (is_object($opts['handler']) && is_callable([$opts['handler'], 'send_message']))
    {
      $this->handler = $opts['handler'];
    }
    elseif (is_string($opts['handler']))
    {
      $classname = $opts['handler'];
      if (strpos($classname, '\\') === False)
      {
        $classname = "\\Lum\\Mailer\\$classname";
      }
      if (class_exists($classname))
      {
        $this->handler = new $classname($this, $opts);
      }
      else
      {
        throw new \Exception("Invalid 'handler' passed to Lum Mailer");
      }
    }
    else
    {
      throw new \Exception("Unsupported 'handler' send to Lum Mailer");
    }
  }

  public function send ($data, $opts=array())
  {
    // First, let's reset our special attributes.
    $this->missing  = array();
    $this->failures = array();

    // Find the template to use.
    if (isset($opts['template']))
      $template = $opts['template'];
    elseif (isset($this->def_template))
      $template = $this->def_template;
    else
      $template = Null; // We're not using a template.

    // If the main template is HTML, we can have a text alternative.
    if (isset($opts['alt_template']))
      $alt_template = $opts['alt_template'];
    elseif (isset($this->alt_template))
      $alt_template = $this->alt_template;
    else
      $alt_template = Null; // No alt template.

    if (is_array($data))
    {
      // Populate the fields for the e-mail message.
      if (isset($this->fields))
      {
        $fields = array();
        foreach ($this->fields as $field=>$required)
        {
          if (isset($data[$field]) && $data[$field] != '')
            $fields[$field] = $data[$field];
          elseif ($required)
            $this->missing[$field] = true;
        }

        // We can only continue if all required fields are present.
        if (count($this->missing))
        { // We have missing values.
          if ($this->log_errors)
          {
            error_log("Message data: ".json_encode($message));
            error_log("Mailer missing: ".json_encode($this->missing));
          }
          return false;
        }
      }
      else
      {
        $fields = $data;
      }
    
      // Are we using templates or not?
      // Templates are highly recommended.
      if (isset($template))
      { // We're using templates (recommended.)
        $message = $this->renderTemplate($template, $fields);
      }
      else
      { // We're not using a template. Build the message manually.
        $message = "---\n";
        foreach ($fields as $field=>$value)
        {
          $message .= " $field: $value\n";
        }
        $message .= "---\n";
      }

      // How about a fallback template?
      if (isset($alt_template))
      {
        $message = [$message];
        $message[] = $this->renderTemplate($alt_template, $fields);
      }
    }
    elseif (is_string($data))
    {
      $message = $data;
    }
    elseif (isset($template))
    {
      $message = $template;
    } 
    else
    {
      if ($this->log_errors)
      {
        error_log("Invalid message in send()");
        return false;
      }
    }

    $sent = $this->handler->send_message($message, $opts);

    if ($this->log_errors && !$sent)
    {
      error_log("Error sending mail, errors: ".serialize($this->failures));
      if ($this->log_message)
        error_log("The message was:\n$message");
    }
    return $sent;
  }

  protected function renderTemplate ($template, $fields)
  {
    $core = \Lum\Core::getInstance();
    $loader = $this->views;
    #error_log("template: '$template', loader: '$loader'");
    if (isset($loader, $core->$loader))
    { // We're using a view loader.
      $message = $core->$loader->load($template, $fields);
    }
    else
    { // View library wasn't found. Assuming a full PHP include file path.
      $message = \Lum\Core::get_php_content($template, $fields);
    } 
    return $message;
  }

}

// End of class.
