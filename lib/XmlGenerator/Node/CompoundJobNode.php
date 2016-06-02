<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'CompoundJobType'.
 */
class CompoundJobNode extends ComplexNode
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
   * @param string     $theName
   */
  public function generateXml( $theXmlWriter, $theName = 'CompoundJob' )
  {
    $theXmlWriter->startElement( $theName );

    parent::generateXml( $theXmlWriter );

    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

