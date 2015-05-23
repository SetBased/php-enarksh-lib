<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Consumption;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class ReadWriteLockConsumption
 *
 * Class for generating XML messages for elements of type 'ReadWriteLockConsumptionType'.
 */
class ReadWriteLockConsumption extends Consumption
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The mode of the lock of this consumption.
   *
   * @var string
   */
  private $myMode;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the consumption.
   * @param string $theMode The mode of the consumption.
   *                        read Read or shared lock on the resource.
   *                        write Write or exclusive lock on the resource.
   */
  public function __construct( $theName, $theMode )
  {
    // xxx Validate $theMode

    $this->myName = (string)$theName;
    $this->myMode = $theMode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    parent::generateXml( $theXmlWriter );

    $theXmlWriter->startElement( 'Mode' );
    $theXmlWriter->text( $this->myMode );
    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  public function getConsumptionTypeTag()
  {
    return 'ReadWriteLockConsumption';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
