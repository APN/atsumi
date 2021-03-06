<?php

class widget_TextConfirmElement extends widget_AbstractElement {

	protected $htmlType 		= 'text';
	protected $confirmText 		= "Please confirm";
	protected $cssClassName 	= 'inputTextConfirm';
	protected $placeholder1		= '';
	protected $placeholder2		= '';

	public function __construct($args) {
		if(array_key_exists("confirmText", $args))
			$this->confirmText = $args['confirmText'];
			
		if(array_key_exists("placeholder1", $args))
			$this->placeholder1 = $args['placeholder1'];
			
		if(array_key_exists("placeholder2", $args))
			$this->placeholder2 = $args['placeholder2'];
	}

	function renderElement() {

		$value = $this->getValue();
		if(!is_array($value)) $value = array();
		if(!array_key_exists(0,$value)) $value[0] = "";
		if(!array_key_exists(1,$value)) $value[1] = "";

		$out = sf("<input type='%s' name='%s[0]' value='%s' placeholder='%s' id='form_%s' class='%s %sPrimary' /><br />",
					$this->htmlType, $this->getName(), parent::makeInputSafe($value[0]), 
					$this->placeholder1, $this->getName(), $this->cssClassName, $this->cssClassName
				);

		$out .= sf("<label for='form_%s' class='rowLabel f08'>%s%s</label>",
					$this->getName(), $this->confirmText,
					($this->getRequired() && !$this->getValidates()) ? $this->goAsterisks() : "");

		$out .= sf("<input type='%s' name='%s[1]' value='%s' placeholder='%s' id='form_%s' class='%s %sSecondary' />",
					$this->htmlType, $this->getName(), parent::makeInputSafe($value[1]), 
					$this->placeholder2, $this->getName(), $this->cssClassName, $this->cssClassName
				);

		return $out;
	}

}

?>