<?php
namespace SetBased\Enarksh\XmlGenerator\Node;

use SetBased\Enarksh\XmlGenerator\Port\Port;

//----------------------------------------------------------------------------------------------------------------------
class SimpleJobNode extends Node
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Purges dependencies of input and output ports.
   */
  protected function purge()
  {
    foreach ($this->myInputPorts as $port)
    {
      $port->purge();
    }

    foreach ($this->myOutputPorts as $port)
    {
      $port->purge();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function ensureDependencies()
  {
    // Noting to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function finalize()
  {
    // Noting to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param Port $thePort
   *
   * @return bool
   */
  protected function hasDependants( $thePort )
  {
    // Noting to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Throws an exception.
   *
   * @param string $theName The name of the child node.
   *
   * @return Node
   */
  public function getChildNode( $theName )
  {
    enk_assert_failed('Mus not be called.');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
