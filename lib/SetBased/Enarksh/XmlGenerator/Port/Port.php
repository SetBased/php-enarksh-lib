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
namespace SetBased\Enarksh\XmlGenerator\Port;

use SetBased\Enarksh\XmlGenerator\Node\Node;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class Port
 * Class for generating XML messages for elements of type 'InputPortType' and 'OutputPortType'.
 *
 * @package SetBased\Enarksh\XmlGenerator\Port
 */
class Port
{

  /**
   * The node of which this port is a port.
   *
   * @var Node
   */
  protected $myNode;

  /**
   * The name of this port.
   *
   * @var string
   */
  protected $myPortName;

  /**
   * The dependencies of this port.
   *
   * @var Port[]
   */
  protected $myPredecessors = array();

  /**
   * The dependants of this port.
   *
   * @var Port[]
   */
  protected $mySuccessors = array();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an Port object.
   *
   * @param Node   $theNode     The node of the port.
   * @param string $thePortName The name of the port.
   */
  public function __construct( $theNode, $thePortName )
  {
    $this->myNode     = $theNode;
    $this->myPortName = (string)$thePortName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add a port as a dependency of this port.
   *
   * @param Port $thePort
   */
  public function addDependency( $thePort )
  {
    /** xxx @todo validate owner of port and owner of this port */

    if (!in_array( $thePort, $this->myPredecessors )) $this->myPredecessors[] = $thePort;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XMLWriter $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    $theXmlWriter->startElement( 'PortName' );
    $theXmlWriter->text( $this->myPortName );
    $theXmlWriter->endElement();

    if (!empty($this->myPredecessors))
    {
      $theXmlWriter->startElement( 'Dependencies' );
      foreach ($this->myPredecessors as $pred)
      {
        $theXmlWriter->startElement( 'Dependency' );
        $pred->generateXmlDependant( $theXmlWriter, $this->myNode->getParent() );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns all the dependencies of this port.
   *
   * @return Port[]
   */
  public function getAllDependencies()
  {
    return $this->myPredecessors;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param bool $theRecursiveFlag
   *
   * @return array
   */
  public function getDependenciesPaths( $theRecursiveFlag = false )
  {
    $ret = array();

    foreach ($this->myPredecessors as $port)
    {
      $ret[] = $port->myNode->getName().'/'.$port->getName();

      if ($theRecursiveFlag)
      {
        $tmp = $port->myNode->getDependenciesPaths( $theRecursiveFlag, $port->getName() );
        $ret = array_merge( $ret, $tmp );
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of this port.
   *
   * @return string
   */
  public function getName()
  {
    return $this->myPortName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the node of this port.
   *
   * @return Node
   */
  public function getNode()
  {
    return $this->myNode;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the node name of this port.
   *
   * @return string
   */
  public function getNodeName()
  {
    return $this->myNode->getName();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes dependencies from this port that are implicit dependencies (via one or more predecessors).
   */
  public function purge()
  {
    // Get all implicit dependencies paths.
    $imp_deps = array();
    foreach ($this->myPredecessors as $port)
    {
      $tmp      = $port->myNode->getDependenciesPaths( true, $port->getName() );
      $imp_deps = array_merge( $imp_deps, $tmp );
    }

    // Create a new dependency array without implicit dependencies.
    $new = array();
    foreach ($this->myPredecessors as $port)
    {
      $path = $port->myNode->getName().'/'.$port->getName();
      if (!in_array( $path, $imp_deps ))
      {
        $new[] = $port;
      }
    }

    $this->myPredecessors = $new;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Replaces any dependency of this port on node $theNodeName with dependencies $theDependencies.
   *
   * @param string $theNodeName
   * @param        $theDependencies
   *
   * @internal param $
   */
  public function replaceNodeDependency( $theNodeName, $theDependencies )
  {
    $obsolete = array();

    // Find any predecessor that depends on node $theNodeName.
    foreach ($this->myPredecessors as $i => $port)
    {
      if ($port->getNodeName()==$theNodeName)
      {
        $obsolete[] = $i;
      }
    }

    if ($obsolete)
    {
      // Remove all dependencies of node $theNodeName.
      foreach ($obsolete as $i)
      {
        unset($this->myPredecessors[$i]);
      }

      // And replace those dependencies with $theDependencies.
      foreach ($theDependencies as $dep)
      {
        $this->myPredecessors[] = $dep;
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param \XmlWriter $theXmlWriter
   * @param Node       $theParentNode
   */
  private function generateXmlDependant( $theXmlWriter, $theParentNode )
  {
    $theXmlWriter->startElement( 'NodeName' );
    $theXmlWriter->text( ($this->myNode===$theParentNode) ? Node::NODE_SELF_NAME : $this->myNode->getName() );
    $theXmlWriter->endElement();

    $theXmlWriter->startElement( 'PortName' );
    $theXmlWriter->text( $this->myPortName );
    $theXmlWriter->endElement();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
