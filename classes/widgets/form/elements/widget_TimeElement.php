<?php

class widget_TimeElement extends widget_AbstractElement {
	private $options = array();

	protected $miniteInterval;
	protected $hourInterval;

	protected $showDefaults = true;

	public function __construct($args) {
		$this->miniteInterval =(isset($args['minuteInterval']) ? $args['minuteInterval'] : 1);
		$this->hourInterval =(isset($args['hourInterval']) ? $args['hourInterval'] : 1);
		$this->showDefaults = (isset($args['showDefaults']) && !$args['showDefaults']) ? false : true;
	}

	function renderElement() {
		$time = $this->getValue();
		$hourValue = $time['hour'];
		$minValue = $time['min'];


	//	if($elementValue == "" || is_null($elementValue))
		if($this->showDefaults) {
			$hourOptions = sfl('<option value="">Hour</option>');
			$minOptions = sfl("<option value=''>Minutes</option>");
		} else {
			$hourOptions = '';
			$minOptions = '';
		}
		for($i = 0; $i <= 23; $i += $this->hourInterval) {
			$text =($i <= 9 ? "0$i" : $i);

			$hourOptions .= sfl(
				"<option value='%s'%s>%s</option>", $i,
				(intval($hourValue) == intval($i) ? ' selected="selected"' : ''),
				$text
			);
		}

		for($i = 0; $i <= 59; $i += $this->miniteInterval) {
			$text =($i <= 9 ? "0$i" : $i);

			$minOptions .= sfl(
				"<option value='%s'%s>%s</option>", $i,
				(intval($minValue) == intval($i) ? ' selected="selected"' : ''),
				$text
			);
		}

		$out = sfl(
			"<select name='%s[hour]' id='form_%s' class='inputTime inputTimeHour' >%s</select>",
			$this->getName(),
			$this->getName(),
			$hourOptions
		);

		$out .=	sfl(
			"<span>:</span> <select name='%s[min]' id='form_%s' style='inputTime inputTimeMinute' >%s</select>",
			$this->getName(),
			$this->getName(),
			$minOptions
		);

		return $out;
	}
}
?>
