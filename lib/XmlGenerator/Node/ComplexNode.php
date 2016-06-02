<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

use SetBased\Enarksh\XmlGenerator\Port\Port;
use SetBased\Enarksh\XmlGenerator\Resource\Resource;

//----------------------------------------------------------------------------------------------------------------------
abstract class ComplexNode extends Node
{

  /**
   * The child nodes of this node.
   *
   * @var Node[]
   */
  protected $myChildNodes = array();

  /**
   * The resources of this node.
   *
   * @var Resource
   */
  protected $myResources = array();

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a node as a child node of this node.
   *
   * @param Node $theChildNode
   */
  public function addChildNode( $theChildNode )
  {
    // @todo Test node exists.
    // @todo Test node is it node zelf.
    // @todo Test parent node is not set.

    $this->myChildNodes[] = $theChildNode;

    $theChildNode->myParent = $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add dependencies between the 'all' input port of this node and the 'all' input port of all this child nodes.
   */
  public function addDependencyAllInputPorts()
  {
    $parent_port = $this->getInputPort( self::ALL_PORT_NAME );

    foreach ($this->myChildNodes as $node)
    {
      $child_port = $node->getInputPort( self::ALL_PORT_NAME );
      $child_port->addDependency( $parent_port );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add dependencies between the 'all' output port of this node and the 'all' output of all this child nodes.
   */
  public function addDependencyAllOutputPorts()
  {
    $parent_port = $this->getOutputPort( self::ALL_PORT_NAME );

    foreach ($this->myChildNodes as $node)
    {
      $child_port = $node->getOutputPort( self::ALL_PORT_NAME );
      $parent_port->addDependency( $child_port );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds resource @a $theResource as a resource of this node.
   *
   * @param \SetBased\Enarksh\XmlGenerator\Resource\Resource
   */
  public function addResource( $theResource )
  {
    // @todo test resource exists.

    $this->myResources[] = $theResource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Does housekeeping to make this node a proper defined node.
   * * Ensures that all required dependencies between the 'all' input and output ports are present
   * * Removes redundant dependencies between ports.
   * * Removes unused ports.
   */
  public function finalize()
  {
    // Ensure that all required dependencies between the 'all' input and output ports are present.
    $this->ensureDependencies();

    // Remove redundant dependencies between ports.
    $this->purge();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Generates XML-code for this node.
   *
   * @param \XMLWriter $theXmlWriter
   */
  public function generateXml( $theXmlWriter )
  {
    parent::generateXml( $theXmlWriter );

    // Generate XML for Resources.
    if (!empty($this->myResources))
    {
      $theXmlWriter->startElement( 'Resources' );
      foreach ($this->myResources as $resource)
      {
        $theXmlWriter->startElement( $resource->getResourceTypeTag() );
        $resource->generateXml( $theXmlWriter );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }

    // Generate XML for child nodes.
    if (!empty($this->myChildNodes))
    {
      $theXmlWriter->startElement( 'Nodes' );
      foreach ($this->myChildNodes as $node)
      {
        $node->preGenerateXml();
        $node->generateXml( $theXmlWriter );
      }
      $theXmlWriter->endElement();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a child node by name
   *
   * @param string $theName The name of the child node.
   *
   * @return Node
   */
  public function getChildNode( $theName )
  {
    $ret = $this->searchChildNode( $theName );
    if ($ret===null) enk_assert_failed( "Child node with name '%s' doesn't exists.", $theName );

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes node @a $theNodeName as a child node. The dependencies of any successor of @a $theNode are been replaced
   * with all dependencies of @a $theNode.
   *
   * @param string $theNodeName
   */
  public function removeChildNode( $theNodeName )
  {
    $node = null;

    // Find and remove node $theNodeName.
    foreach ($this->myChildNodes as $i => $tmp)
    {
      if ($tmp->myName===$theNodeName)
      {
        $node = $tmp;
        unset($this->myChildNodes[$i]);
        break;
      }
    }

    if (!$node) enk_assert_failed( "Node '%s' doesn't have child node '%s'.", $this->getPath(), $theNodeName );

    // Get all dependencies of the node.
    $deps = array();
    foreach ($node->myInputPorts as $port)
    {
      foreach ($port->getAllDependencies() as $dep)
      {
        $deps[] = $dep;
      }
    }

    foreach ($this->myChildNodes as $tmp)
    {
      $tmp->replaceNodeDependency( $theNodeName, $deps );
    }

    foreach ($this->myOutputPorts as $port)
    {
      $port->replaceNodeDependency( $theNodeName, $deps );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * If this node has a child node with name @a $theName that child node will be returned.
   * If no child node with @a $theName exists, returns null.
   *
   * @param string $theName
   *
   * @return null|Node
   */
  public function searchChildNode( $theName )
  {
    $ret = null;
    foreach ($this->myChildNodes as $node)
    {
      if ($node->myName===$theName)
      {
        $ret = $node;
        break;
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function ensureDependencies()
  {
    if ($this->myChildNodes)
    {
      //
      foreach ($this->myChildNodes as $node)
      {
        $node->ensureDependencies();
      }

      // If this node has a output port 'all' ensure that output port 'all' depends on all child nodes.
      //  if ($this->searchOutputPort( self::ALL_PORT_NAME ))
      {
        $this->addDependencyAllOutputPorts();
      }

      // If this node has a input port 'all' ensure input port 'all' depends on output 'all' of each predecessor of
      // this node.
      //  if ($this->searchInputPort( self::ALL_PORT_NAME ))
      {
        $input_port_all = $this->getInputPort( self::ALL_PORT_NAME );
        foreach ($this->myInputPorts as $input_port)
        {
          foreach ($input_port->getAllDependencies() as $port)
          {
            if ($port->getNode()!=$this->myParent)
            {
              $input_port_all->addDependency( $port->getNode()->getOutputPort( self::ALL_PORT_NAME ) );
            }
          }
        }
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if a child node is dependant on an output port.
   *
   * @param Port $thePort The port.
   *
   * @return bool
   */
  protected function hasDependants( $thePort )
  {
    // Test a child node is dependant on the output port.
    foreach ($this->myChildNodes as $node)
    {
      foreach ($node->myInputPorts as $port)
      {
        $tmp = $port->isDependant( $thePort );
        if ($tmp) return true;
      }
    }

    // Test an output port of this node self is dependant on the output port.
    foreach ($this->myOutputPorts as $port)
    {
      $tmp = $port->isDependant( $thePort );
      if ($tmp) return true;
    }

    // Nothing is dependant on the port.
    return false;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes duplicate dependencies and dependencies that are dependencies of a predecessors.
   */
  protected function purge()
  {
    foreach ($this->myInputPorts as $i => $port)
    {
      $port->purge();
    }

    foreach ($this->myOutputPorts as $i => $port)
    {
      $port->purge();
    }

    foreach ($this->myChildNodes as $node)
    {
      $node->purge();
    }

    foreach ($this->myInputPorts as $i => $port)
    {
      if (!$this->hasDependants( $port ))
      {
        unset($this->myInputPorts[$i]);
      }
    }

    foreach ($this->myOutputPorts as $i => $port)
    {
      if ($this->myParent && !$this->myParent->hasDependants( $port ))
      {
        unset($this->myOutputPorts[$i]);
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
