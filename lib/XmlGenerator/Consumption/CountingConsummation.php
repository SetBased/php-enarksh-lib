<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Consumption;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class CountingConsumption
 *
 * Class for generating XML messages for elements of type 'CountingConsumptionType'.
 */
class CountingConsumption extends Consumption
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The amount consumed by this consumption.
   *
   * @var int
   */
  private $myAmount;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName   The name of the consumption.
   * @param int    $theAmount The amount consumed.
   */
  public function __construct( $theName, $theAmount )
  {
    parent::__construct( $theName );

    // xxx Validate $theAmount
    $this->myAmount = $theAmount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    parent::generateXml( $theXmlWriter );

    $theXmlWriter->startElement( 'Amount' );
    $theXmlWriter->text( $this->myAmount );
    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  public function getConsumptionTypeTag()
  {
    return 'CountingConsumption';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
