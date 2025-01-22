<?php 

namespace Lum\Mailer;

/**
 * A base class for functionality common to Transport and Template plugins
 */
abstract class BasePlugin 
{
  protected ?Manager $manager;
  protected array $opts;

  /**
   * Build a plugin
   * 
   * @param ?Manager $manager Manager instance
   * 
   * May be set to null if you plan to pass the instance
   * to a Manager constructor, as it will then call setManager()
   * on the instance.
   * 
   * @param array $opts Options for this plugin instance.
   */
  public function __construct(?Manager $manager, array $opts)
  {
    $this->opts = $opts;
    $this->setManager($manager, false);
  }

  /**
   * Set the manager instance. Shouldn't need to call this manually.
   * 
   * @param Manager $manager
   * @param bool $setOpts (default: true) Set options from the manager.
   * @param bool $overwrite (default: false) Overwrite existing options?
   * @return static
   */
  public function setManager(
    Manager $manager, 
    bool $setOpts=true,
    bool $overwrite=false): static
  {
    $this->manager = $manager;
    if ($setOpts)
    {
      $this->setOptions($manager->getOptions(), $overwrite);
    }
    return $this;
  }

  /**
   * Set options
   * 
   * @param array $opts Associative array of options to set
   * @param bool $overwrite (default: true) Overwrite existing options?
   * @return static
   */
  public function setOptions (array $opts, bool $overwrite=true): static
  {
    foreach ($opts as $key => $val)
    {
      if ($overwrite || !isset($this->opts[$key]))
      {
        $this->opts[$key] = $val;
      }
    }
    return $this;
  }

  /**
   * A method that will be used to do any setup on messages.
   */
  public function setupMessage(Message $msg)
  {
    return;
  }
}
