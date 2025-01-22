<?php

namespace Lum\Mailer\Transport;

use Lum\Mailer\{Manager,Message};
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;

class Symfony extends Plugin
{
  const DEF_DSN = 'sendmail://default';

  protected $symail; // The Symfony Mailer object.

  public function __construct(Manager $manager, array $opts=[])
  {
    parent::__construct($manager, $opts);
    $dsn = $opts['dsn'] ?? static::DEF_DSN;
    $this->symail = Transport::fromDsn($dsn);
  }

  public function setupMessage(Message $msg)
  {
    $msg->transportMsg = new Email();
  }

  public function sendMessage (Message $msg)
  {
    $data = $msg->getData();
    $email = $msg->transportMsg;

    if (isset($data['subject']))
      $email->subject($data['subject']);

    // Find the recipient(s).
    if (isset($data['to']))
      $email->to($data['to']);
    if (isset($data['cc']))
      $email->cc($data['cc']);
    if (isset($data['bcc']))
      $email->bcc($data['bcc']);

    
    if (is_array($msg))
    {
      $html = $msg[0];
      $text = $msg[1];
      $email->setBody($html, 'text/html');
      $email->addPart($text, 'text/plain');
    }
    elseif (is_string($msg))
    {
      if (substr(trim($msg), 0, 1) === '<')
      {
        $email->setBody($msg, 'text/html');
      }
      else
      {
        $email->setBody($msg, 'text/plain');
      }
    }
    else
    {
      throw new \Exception("Unsupported message format");
    }

    return $this->symail->send($email);
  }
  
}
