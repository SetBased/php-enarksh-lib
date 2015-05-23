<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Consumption;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'ConsumptionType'.
 */
abstract class Consumption
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The name of this consumption.
   *
   * @var string
   */
  protected $myName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the consumption.
   */
  public function __construct( $theName )
  {
    $this->myName = (string)$theName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates XML-code for this consumption.
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
   * Returns the XML-tag for the type of this consumption.
   */
  abstract public function getConsumptionTypeTag();

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
