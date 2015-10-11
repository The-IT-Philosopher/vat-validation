<?php
class vatValidation
{
	const WSDL = "http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl";
	private $_client = null;

	private $options  = array(
						'debug' => false,
						);	
	
	private $_valid = false;
	private $_data = array();
	
  private $errors;

	public function __construct($options = array()) {
		
		foreach($options as $option => $value) {
			$this->_options[$option] = $value;
		}
		
		if(!class_exists('SoapClient')) {
			throw new Exception('The Soap library has to be installed and enabled');
		}
				
		try {
			$this->_client = new SoapClient(self::WSDL, array('trace' => true) );
		} catch(Exception $e) {
			$this->trace('Vat Translation Error', $e->getMessage());
		}
	}

	public function check($fullVatNumber) {
    // TODO: 
    // Some countries have planned downtime at a specific time
    // What is the response from the service when this happens?

    try {
      $countryCode = substr($fullVatNumber,0,2);
      $vatNumber   = substr($fullVatNumber,2);
      return $this->checkSplitted($countryCode, $vatNumber);
    } catch (Exceoption $e) {
        if (!(isset($errors))) $errors = array();
        $errors[]=$e;
        return NULL;
    }
  }

	private function checkSplitted($countryCode, $vatNumber) {

		$rs = $this->_client->checkVat( array('countryCode' => $countryCode, 'vatNumber' => $vatNumber) );

		if($this->isDebug()) {
			$this->trace('Web Service result', $this->_client->__getLastResponse());	
		}

		if($rs->valid) {
			$this->_valid = true;
			$this->_data = array(
									'name' => 			$rs->name,
									'address' => 		$rs->address,
								);
			return true;
		} else {
			$this->_valid = false;
			$this->_data = array();
		    return false;
		}
	}

	public function isValid() {
		return $this->_valid;
	}
	
	public function getName() {
		return $this->_data['name'];
	}
	
	public function getAddress() {
		return $this->_data['address'];
	}

  public function getData() {
    $data = array();
    $data['organisation_name'] = $this->_data['name'];
    $data['address']           = $this->_data['address'];
    return $data;
  }
	
	public function isDebug() {
		return ($this->_options['debug'] === true);
	}
	private function trace($title,$body) {
		echo '<h2>TRACE: '.$title.'</h2><pre>'. wordwrap(htmlentities($body)).'</pre>';
	}
	private function cleanUpString($string) {
        for($i=0;$i<100;$i++)
        {               
            $newString = str_replace("  "," ",$string);
            if($newString === $string) {
            	break;
            } else {
            	$string = $newString;
			}
        }
                        
        $newString = "";
        $words = explode(" ",$string);
        foreach($words as $k=>$w)
        {                       
           	$newString .= ucfirst(strtolower($w))." "; 
        }                
        return $newString;
	}
}

?>
