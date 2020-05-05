<?php	
	echo $this->gcharts->LineChart('Earnings')->outputInto('stock_div');
	echo $this->gcharts->div(1000, 400);

	if($this->gcharts->hasErrors())
	{
	    echo $this->gcharts->getErrors();
	}

?>