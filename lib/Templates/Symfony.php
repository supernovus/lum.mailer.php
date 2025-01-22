<?php 

namespace Lum\Mailer\Templates;

use Lum\Mailer\{Manager,Message};

class Symfony extends Plugin 
{
  public function __construct(Manager $manager, array $opts=[])
  {
    parent::__construct($manager, $opts);
  }

  public function renderMessage(Message $message) 
  { 
    // TODO: something here
  }
}
