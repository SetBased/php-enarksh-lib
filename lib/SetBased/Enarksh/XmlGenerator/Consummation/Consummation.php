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
namespace SetBased\Enarksh\XmlGenerator\Consummation;

//----------------------------------------------------------------------------------------------------------------------

/**
 * Class for generating XML messages for elements of type 'ConsummationType'.
 *
 * @package SetBased\Enarksh\XmlGenerator\Consummation
 */
abstract class Consummation
{
  /**
   * The name of this consummation.
   *
   * @var string
   */
  protected $myName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the consummation.
   */
  public function __construct( $theName )
  {
    $this->myName = (string)$theName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates XML-code for this consummation.
   *
   * @param \XmlWriter $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    $theXmlWriter->startElement( 'ResourceName' );
    $theXmlWriter->text( $this->myName );
    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the XML-tag for the type of this consummation.
   */
  abstract public function getConsummationTypeTag();

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
