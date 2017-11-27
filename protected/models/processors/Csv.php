<?php
class Csv {
	private $_delimiter;
	private $_escape;
	private $_output;
	
    function __construct($delimiter = ',', $escape = '\\') {
         $this->_delimiter = $delimiter;
        $this->_escape = $escape;
        $this->_output = '';
    }
    function parseFile($fileName) {
        $output = array();
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $output[] = $data;
            }
            fclose($handle);
        }
        return $output;
    }
    
    function parseFileAssoc($fileName)
    {
        $result = array();
        $data = $this->parseFile($fileName);
        $header = $data[0];
        $rows = count($data);
        $fields = count($header);
        for ($i = 1; $i < $rows; $i++)
        {
            $item = $data[$i];
            $resultItem = array();
            for($j = 0; $j < $fields; $j++)
            {
                $resultItem[$header[$j]] = $item[$j];
            }
            $result[] = $resultItem;
        }
        return $result;
    }

    function escapeField($field) {
    	$index = 0;
    	$out = '';
    	for ($j = 0; $j < strlen($field); $j++) {
    		$char = substr($field, $j, 1);
    		if ($char == $this->_escape || $char == $this->_delimiter) {
    			$out .= substr($field, $index, $j - $index) . $this->_escape . $char;
    			$index = $j + 1;
    		}
    	}
        if ($index < strlen($field)) {
    		$out .= substr($field, $index);
    	}
    	return $out;
    }     
    
	function escapeWithDelimiter($field) {
		return $this->escapeField($field) . $this->_delimiter;
	}
	
    function appendField($field) {
		$this->_output .= $this->escapeField($field);
	}
	
	function appendWithDelimiter($field) {
		$this->_output .= $this->escapeField($field) . $this->_delimiter;
	}

	function output() {
		return $this->_output;
	}
}
?>