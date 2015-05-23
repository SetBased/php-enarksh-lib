<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

use SetBased\Enarksh\XmlGenerator\Consumption\Consumption;
use SetBased\Enarksh\XmlGenerator\Port\InputPort;
use SetBased\Enarksh\XmlGenerator\Port\OutputPort;
use SetBased\Enarksh\XmlGenerator\Port\Port;

//----------------------------------------------------------------------------------------------------------------------
function enk_assert_failed()
{
  $args    = func_get_args();
  $format  = array_shift( $args );
  $message = vsprintf( $format, $args );

  throw new \Exception( $message );
}

//----------------------------------------------------------------------------------------------------------------------
/**
 * Class for generating XML messages for elements of type 'NodeType'.
 */
class Node
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Token for "all" input or output ports on a node.
   */
  const ALL_PORT_NAME = 'all';

  /**
   * Token for node self.
   */
  const NODE_SELF_NAME = '.';

  /**
   * Token for all child nodes.
   */
  const NODE_ALL_NAME = '*';

  /**
   * The child nodes of this node.
   *
   * @var Node[]
   */
  protected $myChildNodes = array();

  /**
   * @var \SetBased\Enarksh\XmlGenerator\Consumption\Consumption[]
   */
  protected $myConsumptions = array();

  /**
   * The input ports of this node.
   *
   * @var InputPort[]
   */
  protected $myInputPorts = array();

  /**
   * The name of this node.
   *
   * @var string
   */
  protected $myName;

  /**
   * The output ports of this node.
   *
   * @var OutputPort[]
   */
  protected $myOutputPorts = array();

  /**
   * The parent node of this node.
   *
   * @var Node
   */
  protected $myParent = null;

  /**
   * The resources of this node.
   *
   * @var \SetBased\Enarksh\XmlGenerator\Resource\Resource[]
   */
  protected $myResources = array();

  /**
   * The user under which this node or its child nodes must run.
   *
   * @var string
   */
  protected $myUserName;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $theName The name of the node.
   */
  public function __construct( $theName )
  {
    $this->myName = (string)$theName;
  }

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
   * Adds a consumption as a consumption of this node.
   *
   * @param Consumption $theConsumption
   */
  public function addConsumption( $theConsumption )
  {
    // @todo test consumption exists.

    $this->myConsumptions[] = $theConsumption;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $theSuccessorNodeName
   * @param string $theSuccessorPortName
   * @param string $thePredecessorNodeName
   * @param string $thePredecessorPortName
   */
  public function addDependency( $theSuccessorNodeName,
                                 $theSuccessorPortName,
                                 $thePredecessorNodeName,
                                 $thePredecessorPortName
  )
  {
    if ($thePredecessorPortName==='') $thePredecessorPortName = self::ALL_PORT_NAME;
    if ($theSuccessorPortName==='') $theSuccessorPortName = self::ALL_PORT_NAME;

    if ($theSuccessorNodeName==self::NODE_SELF_NAME)
    {
      $succ_port = $this->getOutputPort( $theSuccessorPortName );
    }
    else
    {
      $succ_node = $this->getChildNode( $theSuccessorNodeName );
      $succ_port = $succ_node->getInputPort( $theSuccessorPortName );
    }

    if ($thePredecessorNodeName=='.')
    {
      $pred_port = $this->getInputPort( $thePredecessorPortName );
    }
    else
    {
      $pred_node = $this->getChildNode( $thePredecessorNodeName );
      $pred_port = $pred_node->getOutputPort( $thePredecessorPortName );
    }

    $succ_port->addDependency( $pred_port );
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
   * Ensures that all required dependencies between the 'all' input and output ports are present and removes redundant
   * dependencies between ports and nodes.
   */
  public function finalize()
  {
    $this->ensureDependencies();
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
    // Generate XML for the node name.
    $theXmlWriter->startElement( 'NodeName' );
    $theXmlWriter->text( $this->myName );
    $theXmlWriter->endElement();


    // Generate XML for user name.
    if (isset($this->myUserName))
    {
      $theXmlWriter->startElement( 'UserName' );
      $theXmlWriter->text( $this->myUserName );
      $theXmlWriter->endElement();
    }


    // Generate XML for InputPorts.
    if (!empty($this->myInputPorts))
    {
      $theXmlWriter->startElement( 'InputPorts' );
      foreach ($this->myInputPorts as $port)
      {
        $theXmlWriter->startElement( 'Port' );
        $port->generateXml( $theXmlWriter );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }


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


    // Generate XML for Consumptions.
    if (!empty($this->myConsumptions))
    {
      $theXmlWriter->startElement( 'Consumptions' );
      foreach ($this->myConsumptions as $consumption)
      {
        $theXmlWriter->startElement( $consumption->getConsumptionTypeTag() );
        $consumption->generateXml( $theXmlWriter );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }


    // Generate XML for Nodes.
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


    // Generate XML for OutputPorts.
    if (!empty($this->myOutputPorts))
    {
      $theXmlWriter->startElement( 'OutputPorts' );
      foreach ($this->myOutputPorts as $port)
      {
        $theXmlWriter->startElement( 'Port' );
        $port->generateXml( $theXmlWriter );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns child node with @a $theName. If no child node with @a $theName exists an exception is thrown.
   *
   * @param string $theName
   *
   * @return null|Node
   */
  public function getChildNode( $theName )
  {
    $ret = $this->searchChildNode( $theName );
    if ($ret===null) enk_assert_failed( "Child node with name '%s' doesn't exists.", $theName );

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $thePortName
   * @param Port[] $ports
   * @param int    $level
   *
   * @return array
   */
  public function getImplicitDependenciesInputPorts( $thePortName, &$ports, $level )
  {
    $port = $this->getInputPort( $thePortName );

    if (!in_array( $port, $ports, true ))
    {
      if ($level) $ports[] = $port;
      $port->getDependenciesPorts( $ports, $level + 1 );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param  string $thePortName
   * @param Port[]  $ports
   * @param int     $level
   *
   * @return array
   */
  public function getImplicitDependenciesOutputPorts( $thePortName, &$ports, $level )
  {
    $port = $this->getOutputPort( $thePortName );

    if (!in_array( $port, $ports, true ))
    {
      if ($level) $ports[] = $port;
      $port->getDependenciesPorts( $ports, $level + 1 );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns input port with @a $theName. If no input port with @a $theName exists an exception is thrown.
   *
   * @param string $theName
   *
   * @return null|InputPort|Port
   */
  public function getInputPort( $theName )
  {
    $ret = $this->searchInputPort( $theName );

    if ($ret===null)
    {
      if ($theName==self::ALL_PORT_NAME)
      {
        $ret = $this->MakeInputPort( $theName );
      }
      else
      {
        enk_assert_failed( "Node '%s' doesn't have input port '%s'.", $this->myName, $theName ); // xxx use full name
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns all input ports of this node.
   *
   * @return InputPort[]
   */
  public function getInputPorts()
  {
    return $this->myInputPorts;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the name of this node.
   *
   * @return string
   */
  public function getName()
  {
    return $this->myName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns output port with @a $theName. If no output port with @a $theName exists an exception is thrown.
   *
   * @param string $theName
   *
   * @return OutputPort|Port
   */
  public function getOutputPort( $theName )
  {
    $ret = $this->searchOutputPort( $theName );

    if ($ret===null)
    {
      if ($theName==self::ALL_PORT_NAME)
      {
        $ret = $this->makeOutputPort( $theName );
      }
      else
      {
        enk_assert_failed( "Node '%s' doesn't have output port '%s'.", $this->myName, $theName ); // xxx use full name
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns all output ports of this node.
   *
   * @return OutputPort[]
   */
  public function getOutputPorts()
  {
    return $this->myOutputPorts;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the parent node of this node.
   *
   * @return Node
   */
  public function getParent()
  {
    return $this->myParent;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the path of this node.
   *
   * @return string
   */
  public function getPath()
  {
    /** @todo detect recursion */
    return (($this->myParent) ? $this->myParent->getPath() : '/').$this->myName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the user name under which this node or its child nodes must run.
   *
   * @return string
   */
  public function getUserName()
  {
    return $this->myUserName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an input port with name @ $theName and returns that input port.
   *
   * @param string $theName
   *
   * @return Port
   */
  public function makeInputPort( $theName )
  {
    // @todo test port already exists.

    $port                 = new InputPort( $this, $theName );
    $this->myInputPorts[] = $port;

    return $port;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an output port with name @ $theName and returns that output port.
   *
   * @param string $theName
   *
   * @return Port
   */
  public function makeOutputPort( $theName )
  {
    // @todo test port already exists.

    $port                  = new OutputPort( $this, $theName );
    $this->myOutputPorts[] = $port;

    return $port;
  }


  //--------------------------------------------------------------------------------------------------------------------
  /**
   * This function be be called before generation XML and is intended to be overloaded.
   */
  public function preGenerateXml()
  {
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes duplicate dependencies and dependencies that are dependencies of predecessors.
   */
  public function purge()
  {
    foreach ($this->myInputPorts as $port)
    {
      $port->purge();
    }

    foreach ($this->myChildNodes as $node)
    {
      $node->purge();
    }

    foreach ($this->myOutputPorts as $port)
    {
      $port->purge();
    }
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
   * Replaces any dependency of this node on node $theNodeName with dependencies $theDependencies.
   *
   * @param string $theNodeName
   * @param Port[] $theDependencies
   */
  public function replaceNodeDependency( $theNodeName, $theDependencies )
  {
    foreach ($this->myInputPorts as $port)
    {
      $port->replaceNodeDependency( $theNodeName, $theDependencies );
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
  /**
   * If this node has a input port with name @a $theName that input port will be returned.
   * If no input port with @a $theName exists, returns null.
   *
   * @param $theName
   *
   * @return null|InputPort
   */
  public function searchInputPort( $theName )
  {
    $ret = null;
    foreach ($this->myInputPorts as $port)
    {
      if ($port->getName()==$theName)
      {
        $ret = $port;
        break;
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * If this node has a output port with name @a $theName that output port will be returned.
   * If no output port with @a $theName exists, returns null.
   *
   * @param string $theName
   *
   * @return null|OutputPort
   */
  public function searchOutputPort( $theName )
  {
    $ret = null;
    foreach ($this->myOutputPorts as $port)
    {
      if ($port->getName()==$theName)
      {
        $ret = $port;
        break;
      }
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the name of this node to @a $theName.
   *
   * @param string $theName
   */
  public function setName( $theName )
  {
    $this->myName = $theName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Set the user name under which this node or its child nodes must run.
   *
   * @param string $theUserName The user name.
   */
  public function setUserName( $theUserName )
  {
    // @todo Test user name not empty of null.

    $this->myUserName = $theUserName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function ensureDependencies()
  {
    if ($this->myChildNodes)
    {
      $input_port_all = $this->getInputPort( self::ALL_PORT_NAME );
      // $output_port_all = $this->getOutputPort( self::ALL_PORT_NAME );

      foreach ($this->myChildNodes as $node)
      {
        $node->ensureDependencies();
      }

      // Ensure that output port 'all' depends on all child nodes.
      $this->addDependencyAllOutputPorts();

      // Ensure the output port 'all' depends on input port 'all'.
      // $output_port_all->addDependency( $input_port_all );

      // Ensure input port 'all' of this node depends on output 'all' of each predecessor of this node.
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

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
