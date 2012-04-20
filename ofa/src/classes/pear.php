<?php 

require_once "PEAR_WS/Webservice.php";


class ServiceExample extends Services_Webservice
{
  /**
  * Add two integers
  *
  * @param int
  * @param int
  * @return int
  */
  public function addNumbers($num1, $num2)
  {
    return $num1 + $num2;
  }
  
  /**
  *Devuelve Algo
  *
  *@param string
  *@return string
  */
  public function prueba( $nombre) 
  {
	return $nombre;
  }

  private function noService()
  {
  	// boooohhhh!!!
  }
  
  

}

$example = new ServiceExample(
  'service name',
  'service simple description',
  array(
    'uri' => 'ServiceExample',
    'encoding' => SOAP_LITERAL,
    'soap_version' => SOAP_1_2
  )
);

$example->handle();
?>