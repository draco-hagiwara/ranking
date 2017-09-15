<!--
 * グラフ表示script
-->
<script>
jQuery( function() {
    google_pc     = {$plot_data{$gp1}};
    google_mobile = {$plot_data{$gp2}};
    yahoo_pc      = {$plot_data{$gp3}};
    jQuery . jqplot(
        'jqPlot-targetPlot{$cnt}',
        [
        	google_pc, google_mobile, yahoo_pc
        ],
        {
            axes: {
                xaxis: {
                    renderer: jQuery . jqplot . DateAxisRenderer,
                    min: "{$plot_start_date} 0:00AM",
                    max: "{$plot_end_date} 0:00AM",
                    tickOptions: {
                    	showLabel: false,
                    },
                    pad: 0,
                },
                yaxis:{
                  min: 300,
                  max: 1,
                  pad: 10,
                  ticks: [ '300', '250', '200', '150', '100', '50', '1' ],
                  tickOptions: {
                    formatString: '%g'
                  },
                },
            },
            grid: {
                background: '#ffffff',
            },
            //cursor: {
            //    show: false,
            //    showTooltip: false,
            //    zoom: false,
            //},
            seriesColors: [ 'blue', 'green', 'red' ],
            series: [
                { label: 'google_pc' },
                { label: 'google_mobile' },
                { label: 'yahoo_pc' }
            ],
            //seriesDefaults: {
                //lineWidth: 1,
                //markerOptions: {
                //	size: '5',
                	//lineWidth: '5',
                //},
            //},
            highlighter:{
                show: true,
                //showMarker: true,
                //sizeAdjust: 0, // ハイライト時の円の大きさ
            },
            legend: {
                show: true,
                placement: 'inside',
                location: 's',
                renderer: jQuery . jqplot . EnhancedLegendRenderer,
                rendererOptions: {
                    numberRows: 1,
                    seriesToggle: 'fast'
                }
            }
        }
    );
} );
</script>