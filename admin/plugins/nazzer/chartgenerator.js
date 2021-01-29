const chartOptions = {
    showScale: false,
    scaleShowGridLines: false,
    scaleGridLineColor: 'rgba(0,0,0,.05)',
    scaleGridLineWidth: 1,
    scaleShowHorizontalLines: true,
    scaleShowVerticalLines: true,
    bezierCurve: true,
    bezierCurveTension: 0.3,
    pointDot: true,
    pointDotRadius: 4,
    pointDotStrokeWidth: 1,
    pointHitDetectionRadius: 20,
    datasetStroke: true,
    datasetStrokeWidth: 2,
    datasetFill: false,
    legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
    maintainAspectRatio: true,
    responsive: true,
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
    }
};

const pieOptions = {
    segmentShowStroke: true,
    segmentStrokeColor: '#fff',
    segmentStrokeWidth: 2,
    percentageInnerCutout: 50,
    animationSteps: 100,
    animationEasing: 'easeOutBounce',
    animateRotate: true,
    animateScale: false,
    responsive: true,
    maintainAspectRatio: true
};


function newChart(json) {
    chartData = json.chartdata;
    chartType = json.charttype;
    divContainer = $("#" + json.div);

    // Debug Message with JSON
    logMessage("Generating a new chart with parameters: " + JSON.stringify(chartData) + " at divcontainer " + JSON.stringify(json.div), true);

    // Console Log Message
    logMessage("Chart Request Received. Type - " + chartType);

    initChart();

}

function initChart() {
    // Create a 2D Line Chart
    chartCanvas = divContainer.get(0).getContext("2d");
    let chart;

    switch (chartType) {
        case 0:
            chart = generateChart(chartData, chartOptions, "line");
            break;
        case 1:
            chart = generateChart(chartData, chartOptions, "bar");
            break;
        case 2:
            chart = generateChart(chartData, pieOptions, "pie");
            break;
        default:
            logMessage("Defaulting to line chart");
            chart = generateChart(chartData, chartOptions, "line");
            break;
    }
}

function generateChart(chartData, chartOptions, chartType) {
    return new Chart(chartCanvas, {
        type: chartType,
        data: chartData,
        options: chartOptions
    });
}

function logMessage(msg, debug) {
    console.log((debug ? "[Debug] " : "") + "[Nazzer] [ChartGenerator] " + msg);
}