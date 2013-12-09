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

//----------------------------------------------------------------------------------------------------------------------
/** @brief Class for generating XML messages for elements of type 'OutputPortType'.
 */
class OutputPort extends Port
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @return string
   */
  protected function getPortTypeTag()
  {
    return 'OutputPort';
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------

