<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Port;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'OutputPortType'.
 */
class OutputPort extends Port
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
    return $this->myNode->getImplicitDependenciesOutputPorts( $this->myPortName, $ports, $level );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getPortTypeTag()
  {
    return 'OutputPort';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

