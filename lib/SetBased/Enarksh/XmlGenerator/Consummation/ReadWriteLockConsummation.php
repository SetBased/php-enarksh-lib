<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Consummation;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class ReadWriteLockConsummation
 *
 * Class for generating XML messages for elements of type 'ReadWriteLockConsummationType'.
 *
 * @package SetBased\Enarksh\XmlGenerator\Consummation
 */
class ReadWriteLockConsummation extends Consummation
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The mode of the lock of this consummation.
   *
   * @var string
   */
  private $myMode;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the consummation.
   * @param string $theMode The mode of the consummation.
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
  public function getConsummationTypeTag()
  {
    return 'ReadWriteLockConsummation';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
