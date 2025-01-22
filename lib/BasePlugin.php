<?php 

namespace Lum\Mailer;

abstract class BasePlugin 
{
  protected Manager $manager;
  protected array $opts;

  public function __construct(Manager $manager, array $opts=[])
  {
    $this->manager = $manager;
    $this->opts = $opts;
  }

  public function setOptions (array $opts)
  {
    foreach ($opts as $key => $val)
    {
      $this->opts[$key] = $val;
    }
  }
}
