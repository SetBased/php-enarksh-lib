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
/** @brief Class for generating XML messages for elements of type 'CommandJobType'.
 */
class CommandJobNode extends Node
{
  /** The arguments for the executable.
   */
  private $myArgs = array();

  /** The path to the executable that must be run by this job.
   */
  private $myPath;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XMLWriter $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    $theXmlWriter->startElement( 'CommandJob' );

    parent::generateXml( $theXmlWriter );

    $theXmlWriter->startElement( 'Path' );
    $theXmlWriter->text( $this->myPath );
    $theXmlWriter->endElement();

    if ($this->myArgs)
    {
      $theXmlWriter->startElement( 'Args' );
      foreach ($this->myArgs as $arg)
      {
        $theXmlWriter->startElement( 'Arg' );
        $theXmlWriter->text( $arg );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }

    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param bool   $theRecursiveFlag
   * @param string $theOutputPortName
   *
   * @return array
   */
  public function getDependenciesPaths( $theRecursiveFlag, $theOutputPortName )
  {
    /** @todo validate node has only one input and one output port */

    $port = $this->getInputPort( self::ALL_PORT_NAME );

    return $port->getDependenciesPaths( $theRecursiveFlag );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds @a $theArgument to the argument list.
   *
   * @param string $theArgument
   */
  public function setArgument( $theArgument )
  {
    $this->myArgs[] = $theArgument;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Set the path and the arguments based on @a $theCommand. Use this function only when there are no spaces in the
   * path or in any argument.
   *
   * @param string $theCommand
   */
  public function setCommand( $theCommand )
  {
    $parts = explode( ' ', trim( mb_ereg_replace( '[\ \t\n\r\0\x0B\xA0]+', ' ', $theCommand, 'p' ) ) );

    $this->myPath = array_shift( $parts );

    $this->myArgs = $parts;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /** Set the path to the executable that must be run by this job.
   *
   * @param string $thePath The path to the executable that must be run by this job.
   */
  public function setPath( $thePath )
  {
    $this->myPath = $thePath;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
