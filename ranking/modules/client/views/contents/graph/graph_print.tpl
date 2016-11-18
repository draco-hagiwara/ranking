{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>
  {*https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js*}
  {*<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>*}


{* ヘッダー部分　END *}


{* 取りあえず Chrome 対応 *}
{* 参考：http://sakusabe.blogspot.jp/2012/01/css.html *}
{* 参考：http://sakusabe.blogspot.jp/2012/06/js.html *}
<style type="text/css">
<!--
.off
{
display:none;
}-->
</style>


<body>

<H3><p class="bg-success">　　□□□□株式会社 様</p></H3>



{* -- id="myChart3" ------------------------------------------------------------------------------ *}

  {*<div class="form-horizontal col-sm-12">
    <div>
      <canvas id="myChart3" width="400" height="200"></canvas>
    </div>
  </div>*}

<div class="form-horizontal col-sm-11">
      <canvas id="myChart3" width="200" height="100"></canvas>
</div>

<div class="form-horizontal col-xs-11">
<br><br>
{$smarty.now|date_format:"%Y/%m"} 月
    <table class="table table-striped table-hover table-bordered" style="font-size: small;">
      <thead>
        <tr>
          <th>日付</th>
          {foreach from=$tbl_date item=head}
          <th>{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>
            RANK
          </td>
      {foreach from=$tbl_data1 item=data1}
          <td>
            {$data1}
          </td>
      {/foreach}
        </tr>
      </tbody>
    </table>
</div>



<script>
var ctx3 = document.getElementById("myChart3");
var myChart3 = new Chart(ctx3, {
    type: 'line',
    data: {
	    labels: [{$x_data}],
	    //labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
	    datasets: [
	        {
	            label: "Google",
	            fill: false,											// グラフの背景を描画するかどうか
	            lineTension: 0.1,										// ラインのベジェ曲線の張り
	            backgroundColor: "rgba(54, 162, 235, 0.2)",				// ラインの下の塗りつぶしの色
	            borderColor: "rgba(54, 162, 235, 1)",					// 線の色
	            pointHoverRadius: 5,									// グラフの点にカーソルを合わせた時
	            data: [{$set_data1}],
	            spanGaps: true,											// 行がないか、またはヌルのデータと点の間に描画されます
	        },
	    ]
   },
   options: {
        scales: {
            xAxes: [{													// X軸のオプション
                display: false,
                stacked: false,											// 積み上げするかどうか
                gridLines: {
                   display: false										// 目盛を描画するか
                },
                //ticks: {
                //    reverse: true,										// 目盛を反転するか (降順/昇順)
                //    stepSize: 10,
                //}
            }],
            yAxes: [{													// Y軸のオプション
                display: true,
                stacked: false,
                //scaleLabel: {
                //   display: true,										// ラベルを表示するか
                //   labelString: '順位',
                //   fontFamily: 'monospace',
                //   fontSize: 14
                //},
                ticks: {
                   reverse: true,										// 目盛を反転するか (降順/昇順)
                   callback: function(value){
                      return value+'位';
                   },
                   max: 100,
                   min: 1,
                   stepSize: 20,
                }
             }]
        }
   }
});

</script>

{* -------------------------------------------------------------------------------- *}

{* -- id="myChart4" ------------------------------------------------------------------------------ *}

  <div class="form-horizontal col-sm-12">
<br><br><br>
    <div>
      <canvas id="myChart4"></canvas>
    </div>
  </div>



<div class="form-horizontal col-sm-12">
<br><br>
    <table class="table table-striped table-hover table-bordered">
      <thead>
        <tr>
          <th>日付</th>
          {foreach from=$tbl_date item=head}
          <th>{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>
            Yahoo!
          </td>
      {foreach from=$tbl_data2 item=data2}
          <td>
            {$data2}
          </td>
      {/foreach}
        </tr>
      </tbody>

    </table>
</div>



<script>
var ctx4 = document.getElementById("myChart4");
var myChart4 = new Chart(ctx4, {
    type: 'line',
    data: {
	    labels: [{$x_data}],
	    //labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
	    datasets: [
	        {
	            label: "Yahoo!",
	            fill: false,
	            backgroundColor: "rgba(255, 159, 64, 0.2)",
	            borderColor : "rgba(255, 159, 64, 1)",
	            data: [{$set_data2}],
	            //data: [5, 19, 8, 31, 6, 5, 1],
	        }
	    ]
   },
   options: {
        scales: {
            xAxes: [{													// X軸のオプション
                display: false,
                stacked: false,											// 積み上げするかどうか
                gridLines: {
                   display: false										// 目盛を描画するか
                },
                //ticks: {
                //    reverse: true,										// 目盛を反転するか (降順/昇順)
                //    stepSize: 10,
                //}
            }],
            yAxes: [{													// Y軸のオプション
                display: true,
                stacked: false,
                scaleLabel: {
                   display: true,										// ラベルを表示するか
                   labelString: '順位',
                   fontFamily: 'monospace',
                   fontSize: 14
                },
                ticks: {
                   reverse: true,										// 目盛を反転するか (降順/昇順)
                   callback: function(value){
                      return value+'位';
                   },
                   max: 100,
                   min: 1,
                   stepSize: 20,
                }
             }]
        }
   }
});

</script>

{* -------------------------------------------------------------------------------- *}




<br>　<br>　<br>
{* フッター部分　START *}
<div class="panel panel-default off">
  <div class="panel-footer text-center">
    Copyright(C) 2016 - {{$smarty.now|date_format:"%Y"}} Themis Inc. All Rights Reserved.
  </div>
</div>
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
