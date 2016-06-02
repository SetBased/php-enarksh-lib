<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'TerminatorType'.
 *
 * @todo validate node has only one input port and no output ports.
 */
class TerminatorNode extends SimpleJobNode
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

    // A terminator node has only one input and none output port.
    $this->makeInputPort( Node::ALL_PORT_NAME );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
   * @param string     $theName
   */
  public function generateXml( $theXmlWriter, $theName = 'Terminator' )
  {
    $theXmlWriter->startElement( $theName );

    parent::generateXml( $theXmlWriter );

    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
