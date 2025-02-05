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
  const DEFAULT_TRANSPORT = 'Symfony';
  const DEFAULT_TEMPLATES = 'ViewLoader';

  // Options passed to the constructor
  protected array $options;

  protected ?Templates\Plugin $templates;
  protected ?Transport\Plugin $transport;

  /**
   * Level of verbosity of error logs.
   * 
   * 0 = Nothing logged
   * 1 = Known errors logged (default)
   * 
   * More levels with extra details to be added later.
   */
  public int $log_errors = 1;

  public function __construct (array $opts=[])
  { // Save the options as we'll use them a lot!
    $this->options = $opts;
    if (isset($opts['log_errors']) && is_numeric($opts['log_errors']))
    {
      $this->log_errors = intval($opts['log_errors']);
    }
  }

  // Internal method used by getTransport() and getTemplates()
  // to build instances on demand the first time they are requested.
  // This is only required due to limitations in PHP constructors.
  protected function getPluginFor(string $pt, string $dc)
  {
    if (isset($this->$pt))
    {
      return $this->$pt;
    }

    $opts = $this->options;
    $nsp = '\\Lum\\Mailer\\'.ucfirst($pt);

    if (isset($opts[$pt]))
    {
      $ntype = "$nsp\\Plugin";
      $pc = $opts[$pt];

      if ($pc instanceof $ntype)
      { /** @disregard P1006 */
        $pc->setManager($this);
        $this->$pt = $pc;
        return $pc;
      }
      elseif (is_string($pc))
      {
        if (!str_contains($pc, '\\'))
        {
          $pc = "$nsp\\$pc";
        }
      }
      else
      {
        throw new \Exception("Invalid '$pt' option");
      }
    }
    else
    {
      $pc = "$nsp\\$dc";
    }
    
    if (class_exists($pc))
    {
      $pi = new $pc($this, $opts);
      $this->$pt = $pi;
      return $pi;
    }
    else
    {
      throw new \Exception("No such plugin class: $pc");
    }

  }

  public function getOptions(): array
  {
    return $this->options;
  }

  public function getKnownFields(): ?array
  {
    return (
      (isset($this->options['fields']) 
      && is_array($this->options['fields'])) 
      ? $this->options['fields'] 
      : null
    );
  }

  public function setOptions (array $opts): static
  {
    foreach ($opts as $key => $val)
    {
      $this->options[$key] = $val;
    }
    return $this;
  }

  public function getTransport(): Transport\Plugin
  {
    return $this->getPluginFor('transport', static::DEFAULT_TRANSPORT);
  }

  public function getTemplates(): Templates\Plugin
  {
    return $this->getPluginFor('templates', static::DEFAULT_TEMPLATES);
  }

  public function send (array $data=[]): Message
  {
    // Get our transport and template plugins
    $tr = $this->getTransport();
    $tm = $this->getTemplates();

    // Create and setup a message object
    $msg = new Message($data, $this);
    $tr->setupMessage($msg);
    $tm->setupMessage($msg);

    if (!$msg->valid)
    { // Something did not pass validation
      return $msg;
    }
    
    // Render the message template(s)
    $tm->renderMessage($msg);

    // Send the message via the transport
    $tr->sendMessage($msg);

    if (!$msg->sent && $this->log_errors)
    {
      error_log("«Lum-Mailer::SendErrors» ".serialize($msg->failures));
    }

    // We're done here
    return $msg;
  }

}
