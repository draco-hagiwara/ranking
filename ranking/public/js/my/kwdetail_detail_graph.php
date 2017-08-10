<!--
 * Group ＆ Tag設定 script
 *
 * HTML と Script 混在
 *
-->
<script>
var ctx01 = document.getElementById("RankingChart01");
var RankingChart01 = new Chart(ctx01, {
    type: 'line',
    data: {
	    labels: [{$x_data{$info.kw_seq}}],
	    datasets: [
	        {
	            label: "順位の推移",
	            fill: false,										// グラフの背景を描画するかどうか
	            lineTension: 0.1,									// ラインのベジェ曲線の張り
	            backgroundColor: "rgba(54, 162, 235, 0.2)",			// ラインの下の塗りつぶしの色
	            pointBackgroundColor: "rgba(54, 162, 235, 1)", 		// ポインタの色
	            borderColor: "rgba(54, 162, 235, 1)",				// 線の色
	            pointHoverRadius: 5,								// グラフの点にカーソルを合わせた時
	            data: [{$y_data{$info.kw_seq}}],
	            spanGaps: true,										// 行がないか、またはヌルのデータと点の間に描画されます
	        },
	    ]
   },
   options: {
        scales: {
            xAxes: [{												// X軸のオプション
                display: true,
                stacked: false,										// 積み上げするかどうか
                gridLines: {
                display: true										// 目盛を描画するか
                },
            }],
            yAxes: [{												// Y軸のオプション
                display: true,
                stacked: false,
                scaleLabel: {
                   display: true,									// ラベルを表示するか
                   labelString: '順位',
                   fontFamily: 'monospace',
                   fontSize: 14
                },
                ticks: {
                   reverse: true,									// 目盛を反転するか (降順/昇順)
                   //callback: function(value){
                   //   return value+'年月';
                   //},
                   //max: 3000000,
                   min: 1,
                   stepSize: 10,
                }
             }]
        }
   }
});
</script>