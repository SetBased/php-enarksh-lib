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
/** @brief Class for generating XML messages for elements of type 'CountingResourceType'.
 */
class ENK_XmlGeneratorCountingResource extends Resource
{
  /** The amount available of this resource.
   */
  private $myAmount;

  //--------------------------------------------------------------------------------------------------------------------
  /** Creates an ENK_XmlGeneratorCountingResource object.
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
