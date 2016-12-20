{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>
  {*https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js*}
  {*<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>*}

{* ヘッダー部分　END *}

<body>


<div id="contents" class="container">

  <div class="form-horizontal col-sm-12">
    <div>
      <canvas id="SalesChart01" height="150" width="300" ></canvas>
    </div>
  </div>


<script>
var ctx01 = document.getElementById("SalesChart01");
var SalesChart01 = new Chart(ctx01, {
    type: 'line',
    data: {
	    labels: [{$x_data}],
	    datasets: [
	        {
	            label: "月次売上表",
	            fill: true,											// グラフの背景を描画するかどうか
	            lineTension: 0.1,									// ラインのベジェ曲線の張り
	            backgroundColor: "rgba(54, 162, 235, 0.2)",			// ラインの下の塗りつぶしの色
	            borderColor: "rgba(54, 162, 235, 1)",				// 線の色
	            pointHoverRadius: 5,								// グラフの点にカーソルを合わせた時
	            data: [{$y_data}],
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
                   display: true									// 目盛を描画するか
                },
            }],
            yAxes: [{												// Y軸のオプション
                display: true,
                stacked: false,
                scaleLabel: {
                   display: true,									// ラベルを表示するか
                   labelString: '円',
                   fontFamily: 'monospace',
                   fontSize: 14
                },
                ticks: {
                   //reverse: true,									// 目盛を反転するか (降順/昇順)
                   //callback: function(value){
                   //   return value+'年月';
                   //},
                   //max: 3000000,
                   min: 0,
                   stepSize: 1000000,
                }
             }]
        }
   }
});
</script>

 <p></p>

<div class="form-horizontal col-sm-12">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="text-center">月</th>
          {foreach from=$tbl_x_data item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>
            売上金額(円)
          </td>
      {foreach from=$tbl_y_data item=y_data}
          <td class="text-right">
            {$y_data|number_format}
          </td>
      {/foreach}
        </tr>
      </tbody>

    </table>
</div>

</div>


<br><br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
