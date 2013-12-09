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
namespace SetBased\Enarksh\XmlGenerator\Resource;

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating XML messages for elements of type 'ResourceType'.
 */
abstract class Resource
{
  /**
   * The name of this resource.
   *
   * @var string
   */
  protected $myName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the resource.
   */
  public function __construct( $theName )
  {
    $this->myName = (string)$theName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates XML-code for this resource.
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
   * Returns the XML-tag for the type of this resource.
   */
  abstract public function getResourceTypeTag();

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
