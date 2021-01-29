<?php

class Functions
{
    private $global, $template;

    public function __construct($global)
    {
        $this->global = $global;
        $this->template = $global->template;
    }

    // Start DOM Controller
    public function generateAlert($msg, $type) {
        $alert = "";
        switch($type) {
            case 0:
                $alert = "danger";
                break;
            case 1:
                $alert = "success";
                break;
            case 2:
                $alert = "info";
                break;
            default:
                $alert = "dark";
                break;
        }
        return '<div class="alert alert-'.$alert.'" role="alert">'.$msg.'</div>';
    }


    public function generateRandomIdentifier($component, $length = 16, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return $component . "_nazzer_" . implode('', $pieces);
    }

    // End DOM Controller

    // Start Chart Functions
    public function generateChart($chartLabels, $datatypes, $chartTitle = "A chart title", $chartType = 0)
    {
        $randomIdentifier = $this->generateRandomIdentifier("chart");

        $chartJSON = $this->generateChartJSON(
            $chartLabels,
            $randomIdentifier,
            $chartType,
            $datatypes
        );


        $this->template->vars['{charts}'] .= '<div class="row">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">
							' . $chartTitle . '
						</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse">
								<i class="fa fa-minus"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<div class="chart">
							<canvas id="' . $randomIdentifier . '" style="height:250px"></canvas>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
				</div>
				<!-- /.row -->
				';

        $this->template->vars['{footerJS}'] .= '<script>newChart(' . $chartJSON . ')</script>';
    }

    public function generateChartDataSet($data, $label)
    {
        $backgroundColor = array();
        $strokeColor = array();

        for($i = 0; $i < sizeof($data); $i++) {
            $colors = array(rand(1, 255), rand(1, 255), rand(1, 255));
            $backgroundColor[$i] = "rgba(" . $colors[0] . "," . $colors[1] . "," . $colors[2] . ",0.9)";
            $strokeColour[$i] = "rgba(".$colors[0].",".$colors[1].",".$colors[2].",0.8)";
        }

        $dataSet = array(
            "label" => $label,
            "data" => $data,
            "backgroundColor" => $backgroundColor,
            "strokeColor" => $strokeColor,
            "pointColor" => '#3b8bba',
            "pointStrokeColor" => 'rgba(60,141,188,1)',
            "pointHighlightFill" => '#fff',
            "pointHighlightStroke" => 'rgba(60,141,188,1)'
        );
        return $dataSet;
    }

    private function generateChartJSON($chartLabels, $divID, $chartType, $datasets)
    {
        $localDatasets = array();
        for ($i = 0; $i < sizeof($datasets); $i++) {
            $localDatasets[$i] = $datasets[$i];
        }

        $chartData = array(
            "labels" => $chartLabels,
            "datasets" => $localDatasets
        );

        $obj = array(
            "chartdata" => $chartData,
            "div" => $divID,
            "charttype" => $chartType
        );

        return json_encode($obj, JSON_UNESCAPED_SLASHES);
    }

    // End Chart Functions

}

?>