<? if (!defined('BASEPATH')) exit('No direct script access allowed');


class ObjectToXML {

	private $dom;
	
	public function buildXml($obj) {
		
		$this->dom = new DOMDocument("1.0", "UTF8");
		
		$root = $this->dom->createElement(get_class($obj));
		
		foreach($obj as $key=>$value) {
			
			$node = $this->createNode($key, $value);
			if($node != NULL) $root->appendChild($node);
		
		}
		
		$this->dom->appendChild($root);
		
		
		return $this->dom->saveXML();
	
	}

	private function createNode($key, $value) {
		
		echo "trying to create node key:".$key.", value:".$value."<br>";
		
		$node = NULL;

		if(is_string($value) || is_numeric($value) || is_bool($value) || $value == NULL) {

			if($value == NULL) $node = $this->dom->createElement($key);
			else $node = $this->dom->createElement($key, (string)$value);

		} else {
			
			$node = $this->dom->createElement($key);
			
			if($value != NULL) {

				foreach($value as $key=>$value) {
					
					$sub = $this->createNode($key, $value);
					if($sub != NULL)  $node->appendChild($sub);
				
				}
			
			}

		}
		
		return $node;
	}
	
	public function __toString() {
		
		return $this->dom->saveXML();
	}
}

?>