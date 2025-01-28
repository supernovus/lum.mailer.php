<?php 

namespace Lum\Mailer;

use Lum\Mailer\Manager;

/**
 * A simple class representing a message.
 */
class Message 
{
  const HTML_TMPL = 'htmlTemplate';
  const TEXT_TMPL = 'textTemplate';

  protected Manager $manager;
  protected array $data;

  public array $failures = [];
  public array $missing  = [];
  public bool  $valid    = true;
  public bool  $sent     = false;

  /**
   * A transport-specific object storing message state
   */
  public ?object $transportData = null;

  /**
   * A transport-specific send() return value
   */
  public mixed $transportSent = null;

  /**
   * Name/path to the template for HTML messages
   */
  public ?string $htmlTemplate = null;

  /**
   * Name/path to the template for text messages
   */
  public ?string $textTemplate = null;

  /**
   * The HTML message returned by a rendering engine
   */
  public ?string $htmlMessage  = null;

  /**
   * The plain text message returned by a rendering engine
   */
  public ?string $textMessage  = null;

  public function __construct(
    array $data, 
    Manager $manager)
  {
    $opts = $manager->getOptions();

    if (isset($data[static::HTML_TMPL]))
    {
      $this->htmlTemplate = $data[static::HTML_TMPL];
      unset($data[static::HTML_TMPL]);
    }
    elseif (isset($opts[static::HTML_TMPL]))
    {
      $this->htmlTemplate = $opts[static::HTML_TMPL];
    }

    if (isset($data[static::TEXT_TMPL]))
    {
      $this->textTemplate = $data[static::TEXT_TMPL];
      unset($data[static::TEXT_TMPL]);
    }
    elseif (isset($opts[static::TEXT_TMPL]))
    {
      $this->textTemplate = $opts[static::TEXT_TMPL];
    }

    $this->data = $data;
    $this->manager = $manager;
  }

  /**
   * Get the message data (used as template variables)
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

  /**
   * Get the Manager instance
   * @return \Lum\Mailer\Manager
   */
  public function getManager(): Manager
  {
    return $this->manager;
  }

}
