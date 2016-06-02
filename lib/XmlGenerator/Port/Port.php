<?php
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
abstract class Port
{
  //--------------------------------------------------------------------------------------------------------------------
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

    if (!in_array( $thePort, $this->myPredecessors, true )) $this->myPredecessors[] = $thePort;
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
   * @param Port[] $ports
   * @param int    $level
   *
   * @return array
   */
  public function getDependenciesPorts( &$ports, $level )
  {
    foreach ($this->myPredecessors as $port)
    {
      if (!in_array( $port, $ports, true ))
      {
        if ($level) $ports[] = $port;

        $port->getImplicitDependenciesPorts( $ports, $level + 1 );
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return true if this port is dependant on a port.
   *
   * @param Port $thePort The port.
   *
   * @return bool
   */
  public function isDependant( $thePort )
  {
    return in_array( $thePort, $this->myPredecessors, true );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Return true if this port has one or more predecessors.
   *
   * @return bool
   */
  public function hasPredecessor()
  {
    return !empty($this->myPredecessors);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param Port[] $ports
   * @param int    $level
   *
   * @return array
   */
  abstract public function getImplicitDependenciesPorts( &$ports, $level );

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
    // Get all implicit dependencies ports.
    $implicit_dependencies = array();
    foreach ($this->myPredecessors as $port)
    {
      $port->getImplicitDependenciesPorts( $implicit_dependencies, 0 );
    }

    // Create a new dependency array without implicit dependencies.
    $direct_dependencies = array();
    foreach ($this->myPredecessors as $port)
    {
      if (!in_array( $port, $implicit_dependencies, true ))
      {
        // Prevent duplicate dependencies.
        if (!in_array( $port, $direct_dependencies, true ))
        {
          $direct_dependencies[] = $port;
        }
      }
    }

    $this->myPredecessors = $direct_dependencies;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Replaces any dependency of this port on node $theNodeName with dependencies $theDependencies.
   *
   * @param string $theNodeName
   * @param Port[] $theDependencies
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
   * @return string
   */
  abstract protected function getPortTypeTag();

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
