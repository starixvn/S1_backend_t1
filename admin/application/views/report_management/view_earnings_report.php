<?php	
	echo $this->gcharts->LineChart('Earnings')->outputInto('earnings-sec-lc');
	echo $this->gcharts->ColumnChart('Earnings-cc')->outputInto('earnings-sec-cc');
	if($this->gcharts->hasErrors())	{
	    echo $this->gcharts->getErrors();
	}
?>
<div class="row">
	<div class="col-md-11">
		<ul class="nav nav-tabs">
		  <li class="active"><a href="#earnings-sec-table" data-toggle="tab">Table Data</a></li>
		  <li><a href="#earnings-sec-lc" data-toggle="tab">Line Chart</a></li>
		  <li><a href="#earnings-sec-cc" data-toggle="tab">Column Chart</a></li>
		</ul>
	
		<div class="tab-content">
	        <div class="tab-pane active" id="earnings-sec-table">
		        <!-- start table data -->
		        <div class="tab-pane earnings-table-data-monthly" style="margin:20px;">
		        	<div class="pull-center"><div class="tbl-heading-title">2014 Monthly Earnings</div></div>
					<table class="table table-striped table-hover" id="myTable">
						<thead>
							<tr>
								<th class="tbl-heading"></th>
								<th class="tbl-heading">JAN</th>
								<th class="tbl-heading">FEB</th>
								<th class="tbl-heading">MAR</th>
								<th class="tbl-heading">APR</th>
								<th class="tbl-heading">MAY</th>
								<th class="tbl-heading">JUNE</th>
								<th class="tbl-heading">JULY</th>
								<th class="tbl-heading">AUG</th>
								<th class="tbl-heading">SEP</th>
								<th class="tbl-heading">OCT</th>
								<th class="tbl-heading">NOV</th>
								<th class="tbl-heading">DEC</th>
							</tr>
						</thead>

						<tbody>
							<?php
								// if(!empty($rankingLevelData)) {
								// 	foreach($rankingLevelData as $rankingLevelData) {
							?>
										<tr>										
											<td class="tbl-gamename">AG</td>
											<td class="tbl-gameearningsdata">10000</td>
											<td class="tbl-gameearningsdata">5587</td>
											<td class="tbl-gameearningsdata">22568</td>
											<td class="tbl-gameearningsdata">12300</td>
											<td class="tbl-gameearningsdata">156874</td>
											<td class="tbl-gameearningsdata">20120</td>
											<td class="tbl-gameearningsdata">50635</td>
											<td class="tbl-gameearningsdata">846479</td>
											<td class="tbl-gameearningsdata">456456</td>
											<td class="tbl-gameearningsdata">876020</td>
											<td class="tbl-gameearningsdata">23123</td>
											<td class="tbl-gameearningsdata">310568</td>
										</tr>
										<tr>										
											<td class="tbl-gamename">EA</td>
											<td class="tbl-gameearningsdata">5587</td>
											<td class="tbl-gameearningsdata">22568</td>
											<td class="tbl-gameearningsdata">12300</td>
											<td class="tbl-gameearningsdata">156874</td>
											<td class="tbl-gameearningsdata">20120</td>
											<td class="tbl-gameearningsdata">10000</td>
											<td class="tbl-gameearningsdata">5587</td>
											<td class="tbl-gameearningsdata">22568</td>
											<td class="tbl-gameearningsdata">12300</td>
											<td class="tbl-gameearningsdata">156874</td>
											<td class="tbl-gameearningsdata">20120</td>
											<td class="tbl-gameearningsdata">10000</td>
										</tr>
										<tr>										
											<td class="tbl-gamename">PT</td>
											<td class="tbl-gameearningsdata">10000</td>
											<td class="tbl-gameearningsdata">5587</td>
											<td class="tbl-gameearningsdata">22568</td>
											<td class="tbl-gameearningsdata">12300</td>
											<td class="tbl-gameearningsdata">156874</td>
											<td class="tbl-gameearningsdata">20120</td>
											<td class="tbl-gameearningsdata">456456</td>
											<td class="tbl-gameearningsdata">5587</td>
											<td class="tbl-gameearningsdata">22568</td>
											<td class="tbl-gameearningsdata">12300</td>
											<td class="tbl-gameearningsdata">156874</td>
											<td class="tbl-gameearningsdata">20120</td>
											
										</tr>
										<tr>										
											<td class="tbl-gamename">OPUS</td>
											<td class="tbl-gameearningsdata">50635</td>
											<td class="tbl-gameearningsdata">846479</td>											
											<td class="tbl-gameearningsdata">876020</td>
											<td class="tbl-gameearningsdata">23123</td>
											<td class="tbl-gameearningsdata">310568</td>
											<td class="tbl-gameearningsdata">5587</td>
											<td class="tbl-gameearningsdata">22568</td>
											<td class="tbl-gameearningsdata">12300</td>
											<td class="tbl-gameearningsdata">156874</td>
											<td class="tbl-gameearningsdata">20120</td>
											<td class="tbl-gameearningsdata">10000</td>
											<td class="tbl-gameearningsdata">54786</td>
										</tr>
							<?php
								// 	}
								// }
								//else{ ?>
									<!-- <tr>
										<td colspan="13" style="text-align:center">No Records Found
										</td>
									</tr> -->
							<?php	//}
							?>
						</tbody>
					</table>
				</div>
				<!-- end table data monthly -->
		        
		        <!-- start table data yearly -->
		        <div class="tab-pane earnings-table-data-yearly" style="margin:20px;">
		        	<div class="pull-center">
		        		<div class="tbl-heading-title">2014 Yearly Earnings</div>
		        	</div>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th class="tbl-heading"></th>
								<th class="tbl-heading">2014</th>
								<th class="tbl-heading">2015</th>
								<th class="tbl-heading">2016</th>
								<th class="tbl-heading">2017</th>
								<th class="tbl-heading">2018</th>
								<th class="tbl-heading">2019</th>
								<th class="tbl-heading">2020</th>
								<th class="tbl-heading">2021</th>
								<th class="tbl-heading">2022</th>
								<th class="tbl-heading">2023</th>
								<th class="tbl-heading">2024</th>
								<th class="tbl-heading">2025</th>
							</tr>
						</thead>

						<tbody>
							<?php
								// if(!empty($rankingLevelData)) {
								// 	foreach($rankingLevelData as $rankingLevelData) {
							?>
										<tr>										
											<td class="tbl-gamename">AG</td>
											<td class="tbl-gameearningsdata">510000</td>
											<td class="tbl-gameearningsdata">35587</td>
											<td class="tbl-gameearningsdata">422568</td>
											<td class="tbl-gameearningsdata">212300</td>
											<td class="tbl-gameearningsdata">7156874</td>
											<td class="tbl-gameearningsdata">820120</td>
											<td class="tbl-gameearningsdata">850635</td>
											<td class="tbl-gameearningsdata">7846479</td>
											<td class="tbl-gameearningsdata">5456456</td>
											<td class="tbl-gameearningsdata">1876020</td>
											<td class="tbl-gameearningsdata">52323123</td>
											<td class="tbl-gameearningsdata">6310568</td>
										</tr>
										<tr>										
											<td class="tbl-gamename">EA</td>
											<td class="tbl-gameearningsdata">15587</td>
											<td class="tbl-gameearningsdata">522568</td>
											<td class="tbl-gameearningsdata">712300</td>
											<td class="tbl-gameearningsdata">7156874</td>
											<td class="tbl-gameearningsdata">620120</td>
											<td class="tbl-gameearningsdata">410000</td>
											<td class="tbl-gameearningsdata">45587</td>
											<td class="tbl-gameearningsdata">222568</td>
											<td class="tbl-gameearningsdata">812300</td>
											<td class="tbl-gameearningsdata">7156874</td>
											<td class="tbl-gameearningsdata">620120</td>
											<td class="tbl-gameearningsdata">7810000</td>
										</tr>
										<tr>										
											<td class="tbl-gamename">PT</td>
											<td class="tbl-gameearningsdata">810000</td>
											<td class="tbl-gameearningsdata">215587</td>
											<td class="tbl-gameearningsdata">5422568</td>
											<td class="tbl-gameearningsdata">2512300</td>
											<td class="tbl-gameearningsdata">5156874</td>
											<td class="tbl-gameearningsdata">720120</td>
											<td class="tbl-gameearningsdata">7456456</td>
											<td class="tbl-gameearningsdata">785587</td>
											<td class="tbl-gameearningsdata">2542568</td>
											<td class="tbl-gameearningsdata">1265300</td>
											<td class="tbl-gameearningsdata">1568874</td>
											<td class="tbl-gameearningsdata">7208120</td>
											
										</tr>
										<tr>										
											<td class="tbl-gamename">OPUS</td>
											<td class="tbl-gameearningsdata">850635</td>
											<td class="tbl-gameearningsdata">2846479</td>											
											<td class="tbl-gameearningsdata">5876020</td>
											<td class="tbl-gameearningsdata">723123</td>
											<td class="tbl-gameearningsdata">3310568</td>
											<td class="tbl-gameearningsdata">45587</td>
											<td class="tbl-gameearningsdata">722568</td>
											<td class="tbl-gameearningsdata">3212300</td>
											<td class="tbl-gameearningsdata">2156874</td>
											<td class="tbl-gameearningsdata">820120</td>
											<td class="tbl-gameearningsdata">310000</td>
											<td class="tbl-gameearningsdata">954786</td>
										</tr>
							<?php
								// 	}
								// }
								//else{ ?>
									<!-- <tr>
										<td colspan="13" style="text-align:center">No Records Found
										</td>
									</tr> -->
							<?php	//}
							?>
						</tbody>
					</table>
				</div>
				<!-- end table data yearly -->

				<!-- start select period -->
				<div class="row">
					<div class="col-md-2 pull-right">						
						<select id="periodType" class="form-control" name="periodType">
							<option value="0" <?= $this->session->userdata('monthly') == '' ? 'selected' : ''?> selected>Monthly</option>
							<option value="1" <?= $this->session->userdata('yearly') == '' ? 'selected' : ''?>>Yearly</option>							
						 </select>
					</div>
					<div class="pull-right">
						<select id="periodTypeMonthly" class="form-control" name="periodType">
							<option value="2014" <?= $this->session->userdata('monthly_yr1') == '' ? 'selected' : ''?>>2014</option>
							<option value="2015" <?= $this->session->userdata('monthly_yr2') == '' ? 'selected' : ''?>>2015</option>		
							<option value="2016" <?= $this->session->userdata('monthly_yr3') == '' ? 'selected' : ''?>>2016</option>
							<option value="2017" <?= $this->session->userdata('monthly_yr4') == '' ? 'selected' : ''?>>2017</option>
							<option value="2018" <?= $this->session->userdata('monthly_yr5') == '' ? 'selected' : ''?>>2018</option>
							<option value="2019" <?= $this->session->userdata('monthly_yr6') == '' ? 'selected' : ''?>>2019</option>
							<option value="2020" <?= $this->session->userdata('monthly_yr7') == '' ? 'selected' : ''?>>2020</option>
							<option value="2021" <?= $this->session->userdata('monthly_yr8') == '' ? 'selected' : ''?>>2021</option>
							<option value="2022" <?= $this->session->userdata('monthly_yr9') == '' ? 'selected' : ''?>>2022</option>
							<option value="2023" <?= $this->session->userdata('monthly_yr10') == '' ? 'selected' : ''?>>2023</option>
							<option value="2024" <?= $this->session->userdata('monthly_yr11') == '' ? 'selected' : ''?>>2024</option>					
							<option value="2025" <?= $this->session->userdata('monthly_yr12') == '' ? 'selected' : ''?>>2025</option>					
						</select>						
					</div>
				</div>
				<!-- end select period -->
			</div>

			<div class="tab-pane" id="earnings-sec-lc"></div>
	        <div class="tab-pane" id="earnings-sec-cc"></div>
		</div>
		<!-- end tab content -->

		
	</div><!-- end of container -->
</div>
