<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Enarksh\XmlGenerator\Node;

//----------------------------------------------------------------------------------------------------------------------
use SetBased\Enarksh\XmlGenerator\Port\Port;

/**
 * Class for generating XML messages for elements of type 'DynamicJobType'.
 */
class DynamicJobNode extends Node
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var Node
   */
  private $myGeneratorNode;

  /**
   * @var Node
   */
  private $myWorkerNode;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function __construct( $theName )
  {
    parent::__construct( $theName );

    // A dynamic job node has only one input and one output port.
  //  $this->makeInputPort( Node::ALL_PORT_NAME );
  //  $this->makeOutputPort( Node::ALL_PORT_NAME );

    $this->myWorkerNode = new DynamicWorkerNode( 'Worker' );
    $this->myWorkerNode->myParent = $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the generator node of this dynamic node.
   *
   * @param Node $theNode The generator node.
   */
  public function addGeneratorNode( $theNode )
  {
    $this->myGeneratorNode = $theNode;
    $this->myGeneratorNode->myParent = $this;
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function finalize()
  {
   // $this->myGeneratorNode->finalize();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * {@inheritdoc}
   */
  public function generateXml( $theXmlWriter, $theName = 'DynamicJob' )
  {
    $theXmlWriter->startElement( $theName );
    parent::generateXml( $theXmlWriter );

    $this->myGeneratorNode->generateXml( $theXmlWriter, 'Generator' );
    $this->myWorkerNode->generateXml( $theXmlWriter );

    $theXmlWriter->endElement();
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
    if ($this->myGeneratorNode->myName===$theName) return $this->myGeneratorNode;
    if ($this->myWorkerNode->myName===$theName) return $this->myWorkerNode;

    enk_assert_failed( "Child node with name '%s' doesn't exists.", $theName );
  }

  //--------------------------------------------------------------------------------------------------------------------
  protected function ensureDependencies()
  {
    $this->addDependency( $this->myGeneratorNode->myName, '', '.', '' );
    $this->addDependency( $this->myWorkerNode->myName, '', $this->myGeneratorNode->myName, '' );
    $this->addDependency( '.', '', $this->myWorkerNode->myName, '' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param Port $thePort
   *
   * @return bool
   */
  protected function hasDependants( $thePort )
  {
    enk_assert_failed( 'Must not be called.' );
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes duplicate dependencies and dependencies that are dependencies of a predecessors.
   */
  protected function purge()
  {
    // Nothing to do.
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
