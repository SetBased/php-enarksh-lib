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
 * Class CountingConsummation
 *
 * Class for generating XML messages for elements of type 'CountingConsummationType'.
 *
 * @package SetBased\Enarksh\XmlGenerator\Consummation
 */
class CountingConsummation extends Consummation
{
  /**
   * The amount consumed by this consummation.
   *
   * @var int
   */
  private $myAmount;

  //--------------------------------------------------------------------------------------------------------------------
  /** Creates an ENK_XmlGeneratorCountingConsummation object.
   *
   * @param string $theName   The name of the consummation.
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
  public function getConsummationTypeTag()
  {
    return 'CountingConsummation';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
