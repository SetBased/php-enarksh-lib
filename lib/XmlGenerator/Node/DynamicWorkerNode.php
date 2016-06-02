<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

use SetBased\Enarksh\XmlGenerator\Port\Port;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'ManualTriggerType'.
 *
 * @todo validate node has no input ports and only one output port.
 */
class DynamicWorkerNode extends Node
{
  //--------------------------------------------------------------------------------------------------------------------
  public function finalize()
  {
    enk_assert_failed( 'Must not be called.' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    $theXmlWriter->startElement( 'Worker' );

    parent::GenerateXml( $theXmlWriter );

    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $thePortName
   * @param Port[] $ports
   * @param Int    $level
   *
   * @return array
   */
  public function getImplicitDependenciesOutputPorts( $thePortName, &$ports, $level )
  {
    $port = $this->getOutputPort( $thePortName );
    if (!in_array( $port, $ports, true )) if ($level) $ports[] = $port;

    $this->getImplicitDependenciesInputPorts( self::ALL_PORT_NAME, $ports, $level + 1 );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function ensureDependencies()
  {
    enk_assert_failed( 'Must not be called.' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param Port $thePort
   *
   * @return bool
   */
  protected function hasDependants( $thePort )
  {
    enk_assert_failed( 'Must not be called.' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function purge()
  {
    enk_assert_failed( 'Must not be called.' );
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
    enk_assert_failed('Must not be called.');
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

