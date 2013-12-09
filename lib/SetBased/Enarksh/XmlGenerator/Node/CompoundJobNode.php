<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * @author Paul Water
 * @par    Copyright:
 * Set Based IT Consultancy
 * $Date: $
 * $Revision: $
 */
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating XML messages for elements of type 'CompoundJobType'.
 */
class CompoundJobNode extends Node
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    $theXmlWriter->startElement( 'CompoundJob' );
    parent::generateXml( $theXmlWriter );
    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

