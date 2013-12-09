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
/** @brief Class for generating XML messages for elements of type 'NodeType'.
 */
class Node
{
  /**
   * The name of this node.
   *
   * @var string
   */
  protected $myName;

  /**
   * The parent node of this node.
   *
   * @var \SetBased\Enarksh\XmlGenerator\Node\Node
   */
  protected $myParent = null;

  /**
   * The user under which this node or its child nodes must run.
   *
   * @var string
   */
  protected $myUserName;

  /**
   * @var  \SetBased\Enarksh\XmlGenerator\Consummation\Consummation[]
   */
  private $myConsummations = array();

  /**
   * The input ports of this node.
   *
   * @var \SetBased\Enarksh\XmlGenerator\Port\InputPort[]
   */
  private $myInputPorts = array();

  /**
   * The child nodes of this node.
   *
   * @var \SetBased\Enarksh\XmlGenerator\Node\Node[]
   */
  private $myNodes = array();

  /**
   * The output ports of this node.
   *
   * @var \SetBased\Enarksh\XmlGenerator\Port\OutputPort[]
   */
  private $myOutputPorts = array();

  /**
   * The resources of this node.
   *
   * @var \SetBased\Enarksh\XmlGenerator\Resource\Resource[]
   */
  private $myResources = array();

  //--------------------------------------------------------------------------------------------------------------------
  /** Creates an ENK_XmlGeneratorNodeType object.
   *
   * @param string $theName The name of the node.
   */
  public function __construct( $theName )
  {
    $this->myName = (string)$theName;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds node @a $theChildNode as a child node of this node.
   */
  public function addChildNode( $theChildNode )
  {
    // @todo Test node exists.
    // @todo Test node is it node zelf.
    // @todo Test parent node is not set.

    $this->myNodes[] = $theChildNode;

    $theChildNode->myParent = $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds resource @a $theConsummation as a consummation of this node.
   */
  public function addConsummation( $theConsummation )
  {
    // @todo test consummation exists.

    $this->myConsummations[] = $theConsummation;
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
                                 $thePredecessorPortName )
  {
    if ($thePredecessorPortName==='') $thePredecessorPortName = ENK_ALL_PORT_NAME;
    if ($theSuccessorPortName==='') $theSuccessorPortName = ENK_ALL_PORT_NAME;

    if ($theSuccessorNodeName==ENK_NODE_SELF_NAME)
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
  public function addDependicyAllInputPorts()
  {
    $parent_port = $this->getInputPort( ENK_ALL_PORT_NAME );

    foreach ($this->myNodes as $node)
    {
      $child_port = $node->getInputPort( ENK_ALL_PORT_NAME );
      $child_port->addDependency( $parent_port );
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Add dependencies between the 'all' output port of this node and the 'all' output of all this child nodes.
   */
  public function addDependicyAllOutputPorts()
  {
    $parent_port = $this->getOutputPort( ENK_ALL_PORT_NAME );

    foreach ($this->myNodes as $node)
    {
      $child_port = $node->getOutputPort( ENK_ALL_PORT_NAME );
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


    // Generate XML for Consummations.
    if (!empty($this->myConsummations))
    {
      $theXmlWriter->startElement( 'Consummations' );
      foreach ($this->myConsummations as $consummation)
      {
        $theXmlWriter->startElement( $consummation->getConsummationTypeTag() );
        $consummation->generateXml( $theXmlWriter );
        $theXmlWriter->endElement();
      }
      $theXmlWriter->endElement();
    }


    // Generate XML for Nodes.
    if (!empty($this->myNodes))
    {
      $theXmlWriter->startElement( 'Nodes' );
      foreach ($this->myNodes as $node)
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
   * @return null|\SetBased\Enarksh\XmlGenerator\Node\Node
   */
  public function getChildNode( $theName )
  {
    $ret = $this->searchChildNode( $theName );
    if ($ret===null) enk_assert_failed( "Child node with name '%s' doesn't exists.", $theName );

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param  bool   $theRecursiveFlag
   * @param  string $theOutputPortName
   *
   * @return array
   */
  public function getDependenciesPaths( $theRecursiveFlag, $theOutputPortName )
  {
    $ret = array();

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns input port with @a $theName. If no input port with @a $theName exists an exception is thrown.
   *
   * @param string $theName
   *
   * @return null|\SetBased\Enarksh\XmlGenerator\Port\InputPort|\SetBased\Enarksh\XmlGenerator\Port\Port
   */
  public function getInputPort( $theName )
  {
    $ret = $this->searchInputPort( $theName );

    if ($ret===null)
    {
      if ($theName==ENK_ALL_PORT_NAME)
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
   * @return null|\SetBased\Enarksh\XmlGenerator\Port\OutputPort|\SetBased\Enarksh\XmlGenerator\Port\Port
   */
  public function getOutputPort( $theName )
  {
    $ret = $this->searchOutputPort( $theName );

    if ($ret===null)
    {
      if ($theName==ENK_ALL_PORT_NAME)
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
   * Returns the parent node of this node.
   *
   * @return \SetBased\Enarksh\XmlGenerator\Node\Node
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
   * @return \SetBased\Enarksh\XmlGenerator\Port\Port
   */
  public function makeInputPort( $theName )
  {
    // @todo test port already exists.

    $port                 = new \SetBased\Enarksh\XmlGenerator\Port\Port($this, $theName);
    $this->myInputPorts[] = $port;

    return $port;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an output port with name @ $theName and returns that output port.
   *
   * @param string $theName
   *
   * @return \SetBased\Enarksh\XmlGenerator\Port\Port
   */
  public function makeOutputPort( $theName )
  {
    // @todo test port already exists.

    $port                  = new \SetBased\Enarksh\XmlGenerator\Port\Port($this, $theName);
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

    foreach ($this->myNodes as $node)
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
    foreach ($this->myNodes as $i => $tmp)
    {
      if ($tmp->myName===$theNodeName)
      {
        $node = $tmp;
        unset($this->myNodes[$i]);
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

    foreach ($this->myNodes as $tmp)
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
   * @return null|\SetBased\Enarksh\XmlGenerator\Node\Node
   */
  public function searchChildNode( $theName )
  {
    $ret = null;
    foreach ($this->myNodes as $node)
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
   * @return null|\SetBased\Enarksh\XmlGenerator\Port\InputPort
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
   * @return null|\SetBased\Enarksh\XmlGenerator\Port\OutputPort
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
}

//----------------------------------------------------------------------------------------------------------------------
