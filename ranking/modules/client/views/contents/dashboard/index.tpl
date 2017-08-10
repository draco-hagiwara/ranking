{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>

<body>
{* ヘッダー部分　END *}

{form_open('dashboard/manual/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <div class="col-md-6">
      <dl><p class="text-primary">キーワード登録数</p>
        <dt><blockquote><p class="lead">{$keyword_cnt|number_format} 件</p></blockquote></dt>
        <dt>Google : {$google_cnt|number_format} 件</dt>
        <dt>Yahoo! : {$yahoo_cnt|number_format} 件</dt>
      </dl>
    </div>
    <div class="col-md-6">
      <dl><p class="text-primary">ルートドメイン登録数</p>
        <dt><blockquote><p class="lead">{$rootdomain_cnt|number_format} 件</p></blockquote></dt>
      </dl>
    </div>
  </div>

  <div class="form-group form-group-lg">
    <div class="form-horizontal col-md-12">
      <div>
        <canvas id="KeywordcntChart" height="120" width="300" ></canvas>
      </div>
    </div>
  </div>

{form_close()}

<!-- </form> -->





<script>
var ctx01 = document.getElementById("KeywordcntChart");
var KeywordcntChart = new Chart(ctx01, {
    type: 'line',
    data: {
	    labels: [{$kwtran_x_data}],
	    datasets: [
	    	{
	    		label: "キーワード登録数の推移",
	            fill: true,											// グラフの背景を描画するかどうか
	            lineTension: 0.1,									// ラインのベジェ曲線の張り
	            backgroundColor: "rgba(54, 162, 235, 0.2)",			// ラインの下の塗りつぶしの色
	            borderColor: "rgba(54, 162, 235, 1)",				// 線の色
	            pointHoverRadius: 5,								// グラフの点にカーソルを合わせた時
	    		data: [{$kwtran_y_data}],
	    	}
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
                   labelString: '件数',
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
                   stepSize: 100,
                }
             }]
        }
   }
});
</script>



<br><br>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
