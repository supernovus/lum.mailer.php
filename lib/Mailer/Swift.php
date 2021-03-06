<?php

namespace Lum\Mailer;

class Swift
{
  protected $parent;     // The Lum Mailer object.
  protected $mailer;     // The SwiftMailer object.
  public $message;       // The Swift Message object.

  public function __construct ($parent, $opts=[])
  {
    $this->parent = $parent;

    if (isset($opts['transport']))
      $transport = $opts['transport'];
    elseif (isset($opts['host']))
    { // Using SMTP transport.
      $transport = new \Swift_SmtpTransport($opts['host']);
      if (isset($opts['port']))
        $transport->setPort($opts['port']);
      if (isset($opts['enc']))
        $transport->setEncryption($opts['enc']);
      if (isset($opts['user']))
        $transport->setUsername($opts['user']);
      if (isset($opts['pass']))
        $transport->setPassword($opts['pass']);
    }
    else
    { // Using sendmail transport.
      $transport = new \Swift_SendmailTransport();
    }

    $this->mailer = new \Swift_Mailer($transport);

    $this->message = new \Swift_Message();

    if (isset($opts['subject']))
      $this->message->setSubject($opts['subject']);

    if (isset($opts['from']))
      $this->message->setFrom($opts['from']);

    if (isset($opts['to']))
      $this->message->setTo($opts['to']);
    if (isset($opts['cc']))
      $this->message->setCc($opts['cc']);
    if (isset($opts['bcc']))
      $this->message->setBcc($opts['bcc']);
  }

  public function send_message ($message, $opts=[])
  {
    // Find the subject.
    if (isset($opts['subject']))
      $this->message->setSubject($opts['subject']);

    // Find the recipient(s).
    if (isset($opts['to']))
      $this->message->setTo($opts['to']);
    if (isset($opts['cc']))
      $this->message->setCc($opts['cc']);
    if (isset($opts['bcc']))
      $this->message->setBcc($opts['bcc']);

    if (is_array($message))
    {
      $html = $message[0];
      $text = $message[1];
      $this->message->setBody($html, 'text/html');
      $this->message->addPart($text, 'text/plain');
    }
    elseif (is_string($message))
    {
      if (substr($message, 0, 1) === '<')
      {
        $this->message->setBody($message, 'text/html');
      }
      else
      {
        $this->message->setBody($message, 'text/plain');
      }
    }
    else
    {
      throw new \Exception("Unsupported message format");
    }

    return $this->mailer->send($this->message, $this->parent->failures);
  }

}
