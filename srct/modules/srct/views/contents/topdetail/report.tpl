<!DOCTYPE html>
<html class="no-js" lang="jp">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>順位チェックツール &#xB7; SEO-RANK</title>

{* Versionと並び順に注意 *}
<link href="{base_url()}../../css/bootstrap.min.css" rel="stylesheet">

{* FontAwesome *}
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

  <link rel="stylesheet" href="{base_url()}../../js/jqPlot/jquery.jqplot.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="{base_url()}../../css/my/print.css" type="text/css" media="print" />

<script src="{base_url()}../../js/jquery-2.1.4.min.js"></script>
<script src="{base_url()}../../js/bootstrap.min.js"></script>


</head>

<div>
  <section class="container">




{* ヘッダー部分　START *}
  {*include file="../header.tpl" head_index="1"*}

  <script type="text/javascript" src="{base_url()}../../js/jqPlot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.cursor.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.highlighter.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/my/fmsubmit.js"></script>


<body>

{* ヘッダー部分　END *}

<div class="form-group noprint">

{form_open('topdetail/report/' , 'name="detail02Form" class="form-horizontal"')}
  <div class="col-md-1 text-right">
    {* 印刷設定で「余白：最小」「オプション > ヘッダーとフッター：チェックを外す」 *}
    <input type="button" value="印刷する" class="btn btn-success btn-xs" onclick="window.print();" />
  </div>

{form_close()}

{form_open('/data_csv/report_csvdown/' , 'name="detail03Form" class="form-horizontal"')}
  <div class="col-md-1 text-left">
    {$attr['name'] = '_submit'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '↓ ダウンロード' , 'class="btn btn-success btn-xs"')}
  </div>
  {form_hidden('start_date', $plot_start_date)}
  {form_hidden('end_date', $plot_end_date)}
{form_close()}

</div>



<!-- </form> -->


{* 印刷範囲指定の開始 *}

  <br>
  <div class="form-group">
    <H4><p class="bg-success"><u>キーワード&emsp;順位レポート</u></p></H4>
  </div>

  <div class="form-group col-sm-12">

  <div class="form-group noprint">
    <ul class="nav nav-tabs">
      <li role="presentation" {if $term=="1"}class="active"{/if}><a href="/srct/topdetail/report/1/">今月</a></li>
      <li role="presentation" {if $term=="2"}class="active"{/if}><a href="/srct/topdetail/report/2/">前月</a></li>
      <li role="presentation" {if $term=="3"}class="active"{/if}><a href="/srct/topdetail/report/3/">{$gp_month1|string_format:"%2d"}月</a></li>
      <li role="presentation" {if $term=="4"}class="active"{/if}><a href="/srct/topdetail/report/4/">{$gp_month2|string_format:"%2d"}月</a></li>
      <li role="presentation" {if $term=="5"}class="active"{/if}><a href="/srct/topdetail/report/5/">{$gp_month3|string_format:"%2d"}月</a></li>
      <li role="presentation" {if $term=="6"}class="active"{/if}><a href="/srct/topdetail/report/6/">{$gp_month4|string_format:"%2d"}月</a></li>
      <li role="presentation" {if $term=="0"}class="active"{/if}><a href="/srct/topdetail/report/0/">全期間</a></li>
    </ul>
  </div>
  <br>



  {$cnt=0}
  {foreach from=$list_kw item=kw name="cnt"}



  <div class="form-group">
    {if $cnt>=1}<br><br><br>**********************************************************************************************************************************<br><br>{/if}
    <div><label>&emsp;&emsp;■ 対象キーワード設定情報</label></div>
    <div>&emsp;&emsp;・ 検索キーワード：{$info{$cnt}.kw_keyword}</div>
    <div>&emsp;&emsp;・ 対象URL：<a href='{$info{$cnt}.kw_url|unescape:"url"}' target="_blank">{$info{$cnt}.kw_url|unescape:"url"}</a></div>
    <div>&emsp;&emsp;・ URLマッチタイプ：
      {if $info{$cnt}.kw_matchtype==0}完全一致{/if}
      {if $info{$cnt}.kw_matchtype==1}前方一致{/if}
      {if $info{$cnt}.kw_matchtype==2}ドメイン一致{/if}
      {if $info{$cnt}.kw_matchtype==3}ルートドメイン一致 (サブドメイン含む){/if}
    </div>
    <div>&emsp;&emsp;・ ロケーション指定：{$info{$cnt}.kw_location_name}</div>
  </div>

  <hr>

  <div class="form-group">
    <div><label>&emsp;&emsp;■ グラフ表示期間：{$plot_start_date} ～ {$plot_end_date} ({$plot_cnt}日間)</label></div>
  </div>


	  {$gp1=$cnt|cat:"00"}{* google-pc *}
      {$gp2=$cnt|cat:"01"}{* google-mobile *}
      {$gp3=$cnt|cat:"10"}{* yahoo!-pc *}

	  {* グラフ表示領域 *}
	  <p colspan=15 class="jqPlot-area{$cnt}">
	    <div id="jqPlot-targetPlot{$cnt}" style="height: 200px; width: 700px;"></div>
      </p>


	  {* ランク表示 > ただし全期間では非表示 *}
	  {if $term!="0"}
	  <div class="col-xs-11">
	    <table class="table table-striped table-hover table-condensed">
	      <thead>
	        <tr>
	          <th class="text-center">date</th>
	          {foreach from=$tbl_x_data{$gp1} item=head}
	            <th class="text-center">{$head|date_format:"%d"}</th>
	          {/foreach}
	        </tr>
	      </thead>

	      <tbody>
	        <tr>
	          <td class="text-center"><small>G-pc</small></td>
	          {foreach from=$tbl_y_data{$gp1} item=y_data}
	            <td class="text-center">
	              <small>{if $y_data}{$y_data}{else}-{/if}</small>
	            </td>
	          {/foreach}
	        </tr>
	        <tr>
	          <td class="text-center"><small>G-mb</small></td>
	          {foreach from=$tbl_y_data{$gp2} item=y_data}
	            <td class="text-center">
	              <small>{if $y_data}{$y_data}{else}-{/if}</small>
	            </td>
	          {/foreach}
	        </tr>
	        <tr>
	          <td class="text-center"><small>Y-pc</small></td>
	          {foreach from=$tbl_y_data{$gp3} item=y_data}
	            <td class="text-center">
	              <small>{if $y_data}{$y_data}{else}-{/if}</small>
	            </td>
	          {/foreach}
	        </tr>
	      </tbody>
	    </table>
	  </div>
	  {/if}






      {* Graph *}
      {include file="../../../../../public/js/my/top_jqplot_gp.php"}
      {* slideDown *}
      {*include file="../../../../../public/js/my/top_jqplot_show.php"*}




	  {$cnt=$cnt+1}

	{foreachelse}
      キーワード情報はありませんでした。
	{/foreach}


  </div>












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
