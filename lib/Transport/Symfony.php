<?php

namespace Lum\Mailer\Transport;

use Lum\Mailer\{Manager,Message};
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Symfony extends Plugin
{
  const DEF_DSN = 'sendmail://default';

  // The Symfony Mailer object.
  protected $symail;

  // Used by the Symfony template engine.
  public bool $twigTemplates = false;

  public function __construct(?Manager $manager, array $opts=[])
  {
    parent::__construct($manager, $opts);
    $dsn = $opts['dsn'] ?? static::DEF_DSN;
    $this->symail = Transport::fromDsn($dsn);
  }

  public function setupMessage(Message $msg)
  {
    $data = $msg->getData();
    $email = $this->twigTemplates ? new TemplatedEmail() : new Email();

    // Each of these options may be passed directly as message data,
    // or default values specified as transport plugin options. 
    // Each also has a corresponding setter method in the Email class.
    $opts = ['from','subject','to','cc','bcc'];

    foreach ($opts as $opt)
    {
      $val = $data[$opt] ?? $this->opts[$opt] ?? null;
      if (isset($val))
      {
        $email->$opt($val);
      }
    }

    $msg->transportData = $email;
  }

  public function sendMessage (Message $msg)
  {
    $email = $msg->transportData;

    if (isset($msg->textMessage))
    {
      $email->text($msg->textMessage);
    }

    if (isset($msg->htmlMessage))
    {
      $email->html($msg->htmlMessage);
    }

    try 
    {
      $msg->transportSent = $this->symail->send($email);
      $msg->sent = true;
    }
    catch (TransportExceptionInterface $e)
    {
      $msg->failures[] = $e;
    } 
  }
  
}
