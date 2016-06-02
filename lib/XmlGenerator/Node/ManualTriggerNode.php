<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'ManualTriggerType'.
 *
 * @todo validate node has no input ports and only one output port.
 */
class ManualTriggerNode extends SimpleJobNode
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the node.
   */
  public function __construct( $theName )
  {
    parent::__construct( $theName );

    // A manual trigger node has none input and only one output port.
    $this->makeOutputPort( Node::ALL_PORT_NAME );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
   * @param string     $theName
   */
  public function generateXml( $theXmlWriter, $theName = 'ManualTrigger' )
  {
    $theXmlWriter->startElement( $theName );

    parent::GenerateXml( $theXmlWriter );

    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

