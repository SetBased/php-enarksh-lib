<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Port;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'InputPortType'.
 */
class InputPort extends Port
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param Port[] $ports
   * @param int    $level
   *
   * @return array
   */
  public function getImplicitDependenciesPorts( &$ports, $level )
  {
    $this->myNode->getImplicitDependenciesInputPorts( $this->myPortName, $ports, $level );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getPortTypeTag()
  {
    return 'InputPort';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
