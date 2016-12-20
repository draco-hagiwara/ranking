{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>
  {*https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js*}
  {*<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>*}

{* ヘッダー部分　END *}

<body>


<div id="contents" class="container">

<H4><p class="bg-success">担当者別：月次売上表</p></H4>

  <div class="form-horizontal col-sm-12">
    <div>
      <canvas id="SalesChart01" height="120" width="300" ></canvas>
    </div>
  </div>


<script>
var ctx01 = document.getElementById("SalesChart01");
var SalesChart01 = new Chart(ctx01, {
    type: 'line',
    data: {
	    labels: [{$x_data}],
	    datasets: [

		{foreach from=$y_data item=ydata name=mycnt}

		        {
		            label: "{$salse_name[$smarty.foreach.mycnt.index]}",
		            fill: false,
		            backgroundColor: "rgba({$line_color_r[$smarty.foreach.mycnt.index]}, {$line_color_g[$smarty.foreach.mycnt.index]}, {$line_color_b[$smarty.foreach.mycnt.index]}, 0.2)",
		            borderColor : "rgba({$line_color_r[$smarty.foreach.mycnt.index]}, {$line_color_g[$smarty.foreach.mycnt.index]}, {$line_color_b[$smarty.foreach.mycnt.index]}, 1)",
		            data: [{$ydata}],
		        },

		{/foreach}

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
                   stepSize: 500000,
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
          <th class="text-center"></th>
          {foreach from=$tbl_x_data item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
      {foreach from=$salse_name item=name_data name=mycnt}
        <tr>
          <td>
            ■ {$name_data}
          </td>
      {foreach from=$tbl_y_data[$smarty.foreach.mycnt.index] item=y_data}
          <td class="text-right">
            {$y_data|number_format}
          </td>
      {/foreach}
        </tr>
      {/foreach}
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
