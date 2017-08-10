{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script type="text/javascript" src="{base_url()}../../js/jqPlot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.cursor.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.highlighter.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/my/fmsubmit.js"></script>

  <link rel="stylesheet" href="{base_url()}../../js/jqPlot/jquery.jqplot.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="{base_url()}../../css/my/print.css" type="text/css" media="print" />

<SCRIPT language="JavaScript">
<!--
$(function(){
  $('.btn_wk').on('click', function(){
    $(this).toggleClass('active');

      if($(this).hasClass('active')){
        var text = $(this).data('text-clicked');
        $(this).text(text);
        //$('[data-text-clicked]').css('color','orange');
        $('[data-text-clicked]').css('background-color','#f0ad4e');
      } else {
        var text = $(this).data('text-default');
        $(this).text(text);
        //$('[data-text-default]').css('color','black');
        $('[data-text-default]').css('background-color','#5cb85c');
      }
      var text_kw = $(this).data('kw-seq');

      // Ajax通信を開始する
      $.ajax({
          url: '/client/top/watchlist_kw/',
          type: 'post', 					// getかpostを指定(デフォルトは前者)
          dataType: 'json', 				// 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
          data: { 							// 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
        	  chg_seq: text_kw,
              //kwseq : $('#kwseq').val()
          }
      })

      // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
      .done(function (response) {
          $('#result').val('成功');
          $('#detail').val(response.data);
      })
      // ・サーバからステータスコード400以上が返ってきたとき
      // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
      // ・通信に失敗したとき
      .fail(function () {
          $('#result').val('失敗');
          $('#detail').val('');
      });
  });
});
// -->
</SCRIPT>

<body>

{* ヘッダー部分　END *}

<div class="form-group noprint">

{form_open("{$back_page}/search/{$seach_page_no}/" , 'name="detail01Form" class="form-horizontal"')}
  <div class="col-sm-1">
    {$attr['name'] = '_back'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '戻&emsp;&emsp;る' , 'class="btn btn-success btn-xs"')}
  </div>
{form_close()}


{form_open('topdetail/report/' , 'name="detail02Form" class="form-horizontal"')}
  <div class="col-md-1 text-right">
    {* 印刷設定で「余白：最小」「オプション > ヘッダーとフッター：チェックを外す」 *}
    <input type="button" value="印刷する" class="btn btn-success btn-xs" onclick="window.print();" />
  </div>

  {form_hidden('chg_seq', $info.kw_seq)}
{form_close()}


{form_open('/data_csv/report_csvdown/' , 'name="detail03Form" class="form-horizontal"')}
  <div class="col-md-1 text-left">
    {$attr['name'] = '_submit'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '↓ ダウンロード' , 'class="btn btn-success btn-xs"')}
  </div>

  {form_hidden('kw_seq', $info.kw_seq)}
  {form_hidden('gp_kind', $gp_kind)}
  {form_hidden('start_date', $plot_start_date)}
  {form_hidden('end_date', $plot_end_date)}
  {form_hidden('term', $term)}

  {form_hidden('kw_keyword', $info.kw_keyword)}
  {form_hidden('kw_url', $info.kw_url)}
  {form_hidden('kw_searchengine', $info.kw_searchengine)}
{form_close()}


{form_open('topdetail/detail/' , 'name="detail04Form" class="form-horizontal"')}
  <div class="col-md-offset-7 col-md-2 text-right">
    <button type="button" class="btn btn-success btn-xs btn_wk {if $wt_seq}active{/if}" data-text-default="☆ウォッチ" data-text-clicked="★ウォッチ" data-kw-seq={$info.kw_seq} {if $wt_seq}style="background-color:orange;"{/if}>{if $wt_seq}★ウォッチ{else}☆ウォッチ{/if}</button>
    {if $smarty.session.c_memKw==1}
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detail04Form', '/client/topdetail/chg/', 'POST', '{$info.kw_seq}', 'chg_seq');">編&emsp;集</button>
    {/if}
  </div>
{form_close()}

</div>



<!-- </form> -->


{* 印刷範囲指定の開始 *}

  <br>
  <div class="form-group">
    <H4><p class="bg-success"><u>キーワード&emsp;順位レポート</u></p></H4>
  </div>

  <div class="form-group noprint">

    <ul class="nav nav-tabs">
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
          今月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="1-1"}class="active"{/if}><a href="/client/topdetail/report/1-1"><small>今月(GY)</small></a></li>
          <li role="presentation" {if $term=="1-0"}class="active"{/if}><a href="/client/topdetail/report/1-0"><small>今月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          前月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="2-1"}class="active"{/if}><a href="/client/topdetail/report/2-1"><small>前月(GY)</small></a></li>
          <li role="presentation" {if $term=="2-0"}class="active"{/if}><a href="/client/topdetail/report/2-0"><small>前月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {$gp_month1|string_format:"%2d"}月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="3-1"}class="active"{/if}><a href="/client/topdetail/report/3-1"><small>{$gp_month1|string_format:"%2d"}月(GY)</small></a></li>
          <li role="presentation" {if $term=="3-0"}class="active"{/if}><a href="/client/topdetail/report/3-0"><small>{$gp_month1|string_format:"%2d"}月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {$gp_month2|string_format:"%2d"}月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="4-1"}class="active"{/if}><a href="/client/topdetail/report/4-1"><small>{$gp_month2|string_format:"%2d"}月(GY)</small></a></li>
          <li role="presentation" {if $term=="4-0"}class="active"{/if}><a href="/client/topdetail/report/4-0"><small>{$gp_month2|string_format:"%2d"}月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {$gp_month3|string_format:"%2d"}月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="5-1"}class="active"{/if}><a href="/client/topdetail/report/5-1"><small>{$gp_month3|string_format:"%2d"}月(GY)</small></a></li>
          <li role="presentation" {if $term=="5-0"}class="active"{/if}><a href="/client/topdetail/report/5-0"><small>{$gp_month3|string_format:"%2d"}月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {$gp_month4|string_format:"%2d"}月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="6-1"}class="active"{/if}><a href="/client/topdetail/report/6-1"><small>{$gp_month4|string_format:"%2d"}月(GY)</small></a></li>
          <li role="presentation" {if $term=="6-0"}class="active"{/if}><a href="/client/topdetail/report/6-0"><small>{$gp_month4|string_format:"%2d"}月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {$gp_month5|string_format:"%2d"}月 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="7-1"}class="active"{/if}><a href="/client/topdetail/report/7-1"><small>{$gp_month5|string_format:"%2d"}月(GY)</small></a></li>
          <li role="presentation" {if $term=="7-0"}class="active"{/if}><a href="/client/topdetail/report/7-0"><small>{$gp_month5|string_format:"%2d"}月({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
      <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          全期間 <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li role="presentation" {if $term=="0-1"}class="active"{/if}><a href="/client/topdetail/report/0-1"><small>全期間(GY)</small></a></li>
          <li role="presentation" {if $term=="0-0"}class="active"{/if}><a href="/client/topdetail/report/0-0"><small>全期間({if $info.kw_searchengine==0}Google{else}Yahoo!{/if})</small></a></li>
        </ul>
      </li>
    </ul>
  </div>

  <div class="form-group">
    <div><label>&emsp;&emsp;■ 対象キーワード設定情報</label></div>
    <div>&emsp;&emsp;・ 検索キーワード：{$info.kw_keyword}</div>
    <div>&emsp;&emsp;・ 対象URL：<a href='{$info.kw_url|unescape:"url"}' target="_blank">{$info.kw_url|unescape:"url"}</a></div>
    <div>&emsp;&emsp;・ URLマッチタイプ：
      {if $info.kw_matchtype==0}完全一致{/if}
      {if $info.kw_matchtype==1}前方一致{/if}
      {if $info.kw_matchtype==2}ドメイン一致{/if}
      {if $info.kw_matchtype==3}ルートドメイン一致 (サブドメイン含む){/if}
    </div>
    {if $gp_kind==0}<div>&emsp;&emsp;・ 検索エンジン選択：{if $info.kw_searchengine==0}Google{else}Yahoo!{/if}</div>{/if}
    <div>&emsp;&emsp;・ 取得対象デバイス：{if $info.kw_device==0}ＰＣ版{else}モバイル版{/if}</div>
    <div>&emsp;&emsp;・ ロケーション指定：{$info.kw_location_name}</div>
  </div>

  <hr>

  <div class="form-group">
    <div><label>&emsp;&emsp;■ グラフ表示期間：{$plot_start_date} ～ {$plot_end_date} ({$plot_cnt}日間)</label></div>
  </div>

{*include file="../../../../../public/js/my/report_graph.php"*}


  <div class="form-group col-sm-12">
{if $gp_kind==0}

    {* グラフ表示領域 ： 選択グラフ１種類表示 *}
    <div class="form-group col-sm-12">
      <div id="jqPlot-targetPlot" style="height: 500px; width: 900px;"></div>
      {*<div id="jqPlot-targetPlot" style="height: 500px; width: 990px;"></div>*}
    </div>
  </div>

{if $info.kw_searchengine==0}{$plot_data=$plot_data0}{else}{$plot_data=$plot_data1}{/if}
<script>
jQuery( function() {
	plot_data = {$plot_data}
    targetPlot = jQuery . jqplot(
        'jqPlot-targetPlot',
        [
        	plot_data
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
                    //ticks: 10,
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
            //seriesDefaults: {
            //	showMarker: false,
            //    fill: true,
            //	fillAlpha: 0.5,
            //},
        }
    );
} );
</script>

{if $term!="0-0"}

  {if $info.kw_searchengine==0}{$tbl_x_data=$tbl_x_data0}{else}{$tbl_x_data=$tbl_x_data1}{/if}
  {if $info.kw_searchengine==0}{$tbl_y_data=$tbl_y_data0}{else}{$tbl_y_data=$tbl_y_data1}{/if}
  <div class="col-xs-11">
    <table class="table table-striped table-hover table-condensed">
      <thead>
        <tr>
          <th class="text-center">date</th>
          {foreach from=$tbl_x_data item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">rank</td>
      {foreach from=$tbl_y_data item=y_data}
          <td class="text-center">
            <small>{$y_data}</small>
          </td>
      {/foreach}
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-xs-1"></div>

{/if}

{else}

{if !empty($plot_data0)}
{* グラフ表示領域 ： G＆Y グラフ表示 *}

    <div class="form-group col-sm-12">
      <div id="jqPlot-targetPlot" style="height: 500px; width: 900px;"></div>
    </div>

<script>
jQuery( function() {
	plot_data = {$plot_data0}
    targetPlot = jQuery . jqplot(
        'jqPlot-targetPlot',
        [
        	plot_data
        ],
        {
            series: [
                { label: 'Google' },
            ],
            legend: {
                show: true,
                placement: 'outsideGrid',
                location: 'n',
                renderer: jQuery . jqplot . EnhancedLegendRenderer,
                rendererOptions: {
                    numberRows: 1,
                    seriesToggle: 'fast'
                }
            },
            axes: {
                xaxis: {
                    renderer: jQuery . jqplot . DateAxisRenderer,
                    min: "{$plot_start_date} 0:00AM",
                    max: "{$plot_end_date} 0:00AM",
                    //tickInterval: '1 days',
                    tickOptions: {
                    	showLabel: false,
                        //formatString: '%m/%d'
                        //formatString: '%D'
                    },
                    pad: 0,
                    //ticks: 10,
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
            //seriesDefaults: {
            //	showMarker: false,
            //    fill: true,
            //	fillAlpha: 0.5,
            //},
        }
    );
} );
</script>

{if $term!="0-1"}
  <div class="col-xs-11">
    <table class="table table-striped table-hover table-condensed">
      <thead>
        <tr>
          <th class="text-center">date</th>
          {foreach from=$tbl_x_data0 item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">rank</td>
      {foreach from=$tbl_y_data0 item=y_data}
          <td class="text-center">
            <small>{$y_data}</small>
          </td>
      {/foreach}
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-xs-1"></div>
{/if}

</div>
<br><br><br><br><br>

{/if}

{if !empty($plot_data1)}
  <div class="form-group col-sm-12">

    {* グラフ表示領域 *}

    <div class="form-group col-sm-12">
      <div id="jqPlot-targetPlot1" style="height: 500px; width: 900px;"></div>
    </div>



<script>
jQuery( function() {
	plot_data = {$plot_data1}
    targetPlot = jQuery . jqplot(
        'jqPlot-targetPlot1',
        [
        	plot_data
        ],
        {
            series: [
                { label: 'Yahoo!' },
            ],
            legend: {
                show: true,
                placement: 'outsideGrid',
                location: 'n',
                renderer: jQuery . jqplot . EnhancedLegendRenderer,
                rendererOptions: {
                    numberRows: 1,
                    seriesToggle: 'fast'
                }
            },
            axes: {
                xaxis: {
                    renderer: jQuery . jqplot . DateAxisRenderer,
                    min: "{$plot_start_date} 0:00AM",
                    max: "{$plot_end_date} 0:00AM",
                    //tickInterval: '1 days',
                    tickOptions: {
                    	showLabel: false,
                        //formatString: '%m/%d'
                        //formatString: '%D'
                    },
                    pad: 0,
                    //ticks: 10,
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
            //seriesDefaults: {
            //	showMarker: false,
            //    fill: true,
            //	fillAlpha: 0.5,
            //},
        }
    );
} );
</script>

{if $term!="0-1"}
  <div class="col-xs-11">
    <table class="table table-striped table-hover table-condensed">
      <thead>
        <tr>
          <th class="text-center">date</th>
          {foreach from=$tbl_x_data1 item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">rank</td>
      {foreach from=$tbl_y_data1 item=y_data}
          <td class="text-center">
            <small>{$y_data}</small>
          </td>
      {/foreach}
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-xs-1"></div>
{/if}

</div>
{/if}{/if}

{* 印刷範囲指定の終了 *}

<!-- </form> -->


    </div>
  </section>
</div>


<br>

<section class="container noprint">
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}
</section>

<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
