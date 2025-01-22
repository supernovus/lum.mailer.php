<?php

namespace Lum\Mailer;

use Lum\Mailer\Templates;
use Lum\Mailer\Transport;

/**
 * A quick class to send e-mails with.
 * Use it as a standalone component, or extend it for additional features.
 *
 * It requires an underlying mailer plugin/transport.
 *
 * You can use 'Symfony' to use Symphony Mailer or 'SendGrid' to use SendGrid.
 * These are case sensitive since we're using PSR-4 autoloading.
 */
class Manager
{
  // Internal rules.
  protected array|bool|null $fields;

  protected Templates\Plugin $templates;
  protected Transport\Plugin $transport;

  // Set to true to enable logging errors.
  public bool $log_errors  = False;
  public bool $log_message = False;

  public function __construct (array $opts=[])
  {
    // Send False or Null to disable the use of fields.
    if (is_array($opts['fields']))
    {
      $this->fields = $opts['fields'];
    }

    // First initialize the transport plugin
    if (isset($opts['transport']))
    {
      if ($opts['transport'] instanceof Transport\Plugin)
      {
        $this->transport = $opts['transport'];
      }
      elseif (is_string($opts['transport']))
      {
        $classname = $opts['transport'];
        if (!str_contains($classname, '\\'))
        {
          $classname = "\\Lum\\Mailer\\Transport\\$classname";
        }
        if (class_exists($classname))
        {
          $this->transport = new $classname($this, $opts);
        }
        else
        {
          throw new \Exception("No such transport class: $classname");
        }
      }
      else
      {
        throw new \Exception("Invalid 'transport' option");
      }
    }
    else
    {
      $this->transport = new Transport\Symfony($this);
    }

    // Next initialize the templates plugin
    if (isset($opts['templates']))
    {
      if ($opts['templates'] instanceof Templates\Plugin)
      {
        $this->templates = $opts['templates'];
      }
      elseif (is_string($opts['templates']))
      {
        $classname = $opts['templates'];
        if (!str_contains($classname, '\\'))
        {
          $classname = "\\Lum\\Mailer\\Templates\\$classname";
        }
        if (class_exists($classname))
        {
          $this->templates = new $classname($this, $opts);
        }
        else
        {
          throw new \Exception("No such template class: $classname");
        }
      }
      else
      {
        throw new \Exception("Invalid 'templates' option");
      }
    }
    else
    {
      $this->templates = new Templates\ViewLoader($this);
    }

  }

  public function getTransport()
  {
    return $this->transport;
  }

  public function getTemplates()
  {
    return $this->templates;
  }

  public function send ($data, $opts=array())
  {
    // First, let's reset our special attributes.
    $msg = new Message($data, $this);

    // TODO: rewrite this

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

    $sent = $this->transport->sendMessage($message, $opts);

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
