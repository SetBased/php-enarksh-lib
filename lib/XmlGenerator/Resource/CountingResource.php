<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Resource;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'CountingResourceType'.
 */
class CountingResource extends Resource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The amount available of this resource.
   *
   * @var int
   */
  private $myAmount;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName   The name of the resource.
   * @param string $theAmount The amount available of this resource..
   */
  public function __construct( $theName, $theAmount )
  {
    parent::__construct( $theName );

    // xxx Validate $theAmount
    $this->myAmount = $theAmount;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
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
  public function getResourceTypeTag()
  {
    return 'CountingResource';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
