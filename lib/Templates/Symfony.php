<?php 

namespace Lum\Mailer\Templates;

use Lum\Mailer\{Manager,Message};
use Lum\Mailer\Transport\Symfony as SymfonyTransport;

class Symfony extends Plugin 
{
  public function setManager(
    Manager $manager, 
    bool $setOpts = true, 
    bool $overwrite = false): static
  {
    $transport = $manager->getTransport();
    
    if (!($transport instanceof SymfonyTransport))
    {
      throw new \Exception("Symfony templates require Symfony transport");
    }

    $transport->twigTemplates = true;

    return parent::setManager($manager, $setOpts, $overwrite);
  }

  public function renderMessage(Message $msg) 
  { 
    $email = $msg->transportData;

    if (isset($msg->htmlTemplate))
    {
      $email->htmlTemplate($msg->htmlTemplate);
    }

    if (isset($msg->textTemplate))
    {
      $email->textTemplate($msg->textTemplate);
    }

    if (isset($this->opts['locale']))
    {
      $email->locale($this->opts['locale']);
    }

    $email->context($msg->getData());
  }
}
