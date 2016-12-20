{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>
  {*https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js*}
  {*<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>*}

  <script type="text/javascript" src="https://{$smarty.server.SERVER_NAME}/js/jQuery.jPrintArea.js"></script>{* 印刷プレビュー表示 *}
{* 印刷プレビュー表示 *}
<script type="text/javascript">
$(function(){
  $('#btn_print1').click(function(){
    $.jPrintArea(".print-area");
  });

  $('#btn_print2').click(function(){
    $.jPrintArea(".print-area");
  });
});
</script>


  <script type="text/javascript" src="https://{$smarty.server.SERVER_NAME}/js/printThis.js"></script>{* 印刷プレビュー表示 *}

  <script type="text/javascript" src="https://{$smarty.server.SERVER_NAME}/js/jquery.printelement.min.js"></script>{* 印刷プレビュー表示 *}


{* ヘッダー部分　END *}



<body>

<H3><p class="bg-success">　　グラフ表示</p></H3>


{form_open('graph/createpdf/' , 'name="pdfForm" class="form-horizontal"')}

  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-10">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">PDF作成</button>
    </div>
  </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ＰＤＦ　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='pdf' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<div id="print-coupon">グラフを印刷する (printThis)</div>
<div id="print-graph">グラフを印刷する (printelement)</div>


{* -- id="myChart3" ------------------------------------------------------------------------------ *}

<p class="text-right">
  <input type="button" id="btn_print1" value="グラフを印刷">
</p>
{* 印刷範囲を設定 start *}
<div  id="graph-image">
<div  id="coupon-image">
<div class="print-area">
  <div class="form-horizontal col-sm-12">
    <div>
      <canvas id="myChart3"></canvas>
    </div>
  </div>



<br>　<br>　<br>
<div class="form-horizontal col-sm-12">
    <table class="table table-striped table-hover">
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
            Google
          </td>
      {foreach from=$tbl_data1 item=data1}
          <td>
            {$data1}
          </td>
      {/foreach}
        </tr>
      </tbody>
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

</div>
</div>
</div>


<script>
$('#print-coupon').click(function(){
    $('#coupon-image').printThis();
});
</script>

<script>
$('#print-graph').click(function(){
    $('#graph-image').printThis();
});
</script>



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



{* -- id="myChart4" ------------------------------------------------------------------------------ *}

<br>　<br>　<br>
<div class="form-horizontal col-md-12">
    <table class="table table-striped table-hover table-bordered">
      <thead>
        <tr>
          <th>日</th>
          {foreach from=$tbl_date item=head}
          <th>{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>
            G
          </td>
          {foreach from=$tbl_data1 item=data1}
            <td>
              {$data1}
            </td>
          {/foreach}
        </tr>
      </tbody>
      <tbody>
        <tr>
          <td>
            Y!
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

<div style="width:30%">
  <canvas id="myChart4"></canvas>
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
	            label: "Google",
	            fill: false,											// グラフの背景を描画するかどうか
	            lineTension: 0.1,										// ラインのベジェ曲線の張り
	            backgroundColor: "rgba(54, 162, 235, 0.2)",				// ラインの下の塗りつぶしの色
	            borderColor: "rgba(54, 162, 235, 1)",					// 線の色
	            pointHoverRadius: 5,									// グラフの点にカーソルを合わせた時
	            data: [{$set_data1}],
	            spanGaps: true,											// 行がないか、またはヌルのデータと点の間に描画されます
	        },
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
                //scaleLabel: {
                //   display: true,										// ラベルを表示するか
                //   labelString: '順位',
                //   fontFamily: 'monospace',
                //   fontSize: 14
                //},
                ticks: {
                   reverse: true,										// 目盛を反転するか (降順/昇順)
                   //callback: function(value){
                   //   return value+'位';
                   //},
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






{* -- id="chart1" ------------------------------------------------------------------------------ *}
<hr>
{*<div style="width:30%">
  <div>
    <canvas id="chart1" width="600" height="300"></canvas>
</div></div>
*}

<script>
//jQueryの場合
//var ctx = $("#chart1").get(0).getContext("2d");
//普通のjavascriptの場合
//var ctx = document.getElementById("chart1").getContext("2d");
var data = {
  //X軸のラベル
  labels : ["January","February","March","April","May","June","July"],
  datasets : [
    {
      //1つ目のグラフの描画設定
      fillColor : "rgba(220,220,220,0.5)",//面の色・透明度
      strokeColor : "rgba(220,220,220,1)",//線の色・透明度
      pointColor : "rgba(220,220,220,1)", //点の色・透明度
      pointStrokeColor : "#fff",//点の周りの色
      data : [145,159,130,141,156,155,140]//labelごとのデータ
    },
    {
      //2つ目のグラフの描画設定
      fillColor : "rgba(151,187,205,0.5)",
      strokeColor : "rgba(151,187,205,1)",
      pointColor : "rgba(151,187,205,1)",
      pointStrokeColor : "#fff",
      data : [115,159,130,111,156,155,140]
    }
  ]
}
var option = {
  //Boolean - 縦軸の目盛りの上書き許可
  scaleOverride : true,
  //** ↑がtrueの場合 **
  //Number - 目盛りの間隔
  scaleSteps : 6,
  //Number - 目盛り区切りの間隔
  scaleStepWidth : 10,
  //Number - 目盛りの最小値
  scaleStartValue : 100,
  //String - 目盛りの線の色
  scaleLineColor : "rgba(0,0,0,.1)",
  //Number - 目盛りの線の幅
  scaleLineWidth : 10,
  //Boolean - 目盛りを表示するかどうか
  scaleShowLabels : true,
  //String - 目盛りのフォント
  scaleFontFamily : "'Arial'",
  //Number - 目盛りのフォントサイズ
  scaleFontSize : 10,
  //String - 目盛りのフォントスタイル bold→太字
  scaleFontStyle : "normal",
  //String - 目盛りのフォント
  scaleFontColor : "#666",
  ///Boolean - チャートの背景にグリッドを描画するか
  scaleShowGridLines : true,
  //String - チャート背景のグリッド色
  scaleGridLineColor : "rgba(0,0,0,.05)",
  //Number - チャート背景のグリッドの太さ
  scaleGridLineWidth : 1,
  //Boolean - 線を曲線にするかどうか。falseで折れ線になる
  bezierCurve : true,
  //Boolean - 点を描画するか
  pointDot : true,
  //Number - 点の大きさ
  pointDotRadius : 3,
  //Number - 点の周りの大きさ
  pointDotStrokeWidth : 1,
  //Number - 線の太さ
  datasetStrokeWidth : 2,
  //Boolean - アニメーションの有無
  animation : true,
  //Number - アニメーションの早さ(大きいほど遅い)
  animationSteps : 60,
  //Function - アニメーション終了時の処理
  onAnimationComplete : null
}
//グラフを描画する
//var myNewChart = new Chart(ctx).Line(data,option);
//optionは無くても描画可能
//var myNewChart = new Chart(ctx).Line(data);
</script>

{* -------------------------------------------------------------------------------- *}


{* -- id="chart1" ------------------------------------------------------------------------------ *}
<br>　<br>　<br>
<hr>
<div style="width:30%">
  <div>
    <canvas id="myChart1" width="600" height="300"></canvas>
</div></div>

<script>
var ctx1 = document.getElementById("myChart1");
var myChart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>

{* -------------------------------------------------------------------------------- *}

{* -- id="chart1" ------------------------------------------------------------------------------ *}
<hr>
<div style="width:30%">
  <div>
    <canvas id="myChart2" width="600" height="300"></canvas>
</div></div>

<script>
var ctx2 = document.getElementById("myChart2");
var myChart2 = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>

{* -------------------------------------------------------------------------------- *}














<br><br><br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
