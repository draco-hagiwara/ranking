<!--
 * グラフ表示script
 *
 * jsでデータを上手く受けられない？
 *
-->
<script>
jQuery( function() {
    var google_pc = {$plot_data000};
    var google_mobile = {$plot_data001};
    var yahoo_pc = {$plot_data010};
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
                    //tickInterval: '1 days',
                    tickOptions: {
                    	showLabel: false,
                    	//show: false,
                        //formatString: '%m/%d',
                        //formatString: '%D',
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
            cursor: {
                show: false,
                showTooltip: false,
                zoom: false,
            },
            highlighter:{
                show: true,
                sizeAdjust: 7.5 // ハイライト時の円の大きさ
            },
            series: [
                { label: 'Google-pc' },
                { label: 'Google-mobile' },
                { label: 'Yahoo!-pc' }
            ],
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