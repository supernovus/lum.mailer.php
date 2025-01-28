<?php

namespace Lum\Mailer\Transport;

use Lum\Mailer\{Manager,Message};
use Symfony\Component\Mime\{Email,Address};
use Symfony\Component\Mailer\{Mailer,MailerInterface,Transport};
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Symfony extends Plugin
{
  const DEF_DSN = 'sendmail://default';

  // The Symfony Mailer object.
  protected MailerInterface $symail;

  // The Symfony Mailer Transport object.
  protected TransportInterface $symtrans;

  // Used by the Symfony template engine.
  public bool $twigTemplates = false;

  public function __construct(?Manager $manager, array $opts=[])
  {
    parent::__construct($manager, $opts);
    $dsn = $opts['dsn'] ?? static::DEF_DSN;
    $this->symtrans = Transport::fromDsn($dsn);
    $this->symail = new Mailer($this->symtrans);
  }

  public function setupMessage(Message $msg)
  {
    $data = $msg->getData();
    $email = $this->twigTemplates ? new TemplatedEmail() : new Email();

    // Each of these options may be passed directly as message data,
    // or default values specified as transport plugin options. 
    // Each also has a corresponding setter method in the Email class.
    $opts = 
    [
      'from'      => 1,
      'subject'   => 0,
      'to'        => 3,
      'cc'        => 3,
      'bcc'       => 3,
    ];

    foreach ($opts as $opt => $oot)
    {
      $val = $data[$opt] ?? $this->opts[$opt] ?? null;
      if (isset($val))
      {
        if ($oot & 2)
        { // Multiple arguments supported
          if (!is_array($val)) $val = [$val];
          if ($oot & 1)
          { // Email addresses
            $val = Address::createArray($val);
          }
          $email->$opt(...$val);
        }
        else
        { // Only one argument
          if ($oot & 1)
          { // A single email address
            $val = Address::create($val);
          }
          $email->$opt($val);
        }
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
      $msg->failures[] = $e->getMessage();
      $msg->failures[] = $e->getTraceAsString();
    }
  }
  
}
