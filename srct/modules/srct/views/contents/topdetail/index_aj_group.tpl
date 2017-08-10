  <link href="{base_url()}../../css/my/top.css" rel="stylesheet">

  <script type="text/javascript" src="{base_url()}../../js/jqPlot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.cursor.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.highlighter.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>

  <link rel="stylesheet" href="{base_url()}../../js/jqPlot/jquery.jqplot.min.css" type="text/css" media="screen">

    <table class="rank_tb"><br />KW件数： {$list_cnt}件
	<tr><!--各テーブルヘッダはクリックするとソートする。-->
	  <th><input type="checkbox" id="checkbox_all" /></th><!--一括チェック＆解除。チェックしたものが処理対象になる。-->
	  <th><img src="/images/user/wl.png" /></th><!--一括ウォッチリストではない。単なるソート-->
	  <th class="t_l">グループ</th>
	  <th class="t_l">キーワード</th>
	  <th colspan="2"><!--URLは一定文字数以上は省略する。30文字程度？-->
		<p class="f_l no_space">URL</p>
		<p class="f_r no_space">マッチタイプ</p>
	  </th>
	  <th>地域</th>
	  <th colspan="4"><img src="/images/user/monitor.png" width="16" /><img src="/images/user/google_favicon.png" /></th>
	  <th colspan="2"><img src="/images/user/smartphone.png" width="16" /><img src="/images/user/google_favicon.png" /></th>
	  <th colspan="2"><img src="/images/user/monitor.png" width="16" style="margin-right: 2px;" /><img src="/images/user/yahoo_favicon.png" /></th>
	</tr>

	{$cnt=0}
	{foreach from=$list_kw item=kw name="cnt"}

	  {$gp1=$cnt|cat:"00"}{* google-pc *}
      {$gp2=$cnt|cat:"01"}{* google-mobile *}
      {$gp3=$cnt|cat:"10"}{* yahoo!-pc *}

	<tr>
	  <td class="checkbox_kw{$cnt}" id="chktest"><input type="checkbox" name="checkbox_kwseq[]" value="{$kw.kw_seq}" /></td>
	  <td>
	    <label class="btn_wk {if $kw.wt_seq}active{/if}" data-text-default="☆" data-text-clicked="★" data-kw-seq={$kw.kw_seq}>{if $kw.wt_seq}<img id="watchlist_kw{$kw.kw_seq}" src="/images/user/wl_on.png" />{else}<img id="watchlist_kw{$kw.kw_seq}" src="/images/user/wl.png" />{/if}</label>
	  </td>
	  <td class="t_l">{if $kw.kw_group}{$kw.kw_group}{else}-{/if}</td>
	  <td class="t_l">{$kw.kw_keyword}</td>
	  <td class="t_l"><a href="{$kw.kw_url}" target="_blank">{$kw.kw_url}</a></td>
	  <td>
	    {if {$kw.kw_matchtype==0}}<img src="/images/user/p1.png" title="完全一致" />
	    {elseif {$kw.kw_matchtype==1}}<img src="/images/user/p2.png" title="前方一致" />
	    {elseif {$kw.kw_matchtype==2}}<img src="/images/user/p3.png" title="ドメイン一致" />
	    {elseif {$kw.kw_matchtype==3}}<img src="/images/user/p4.png" title="ルートドメイン一致" />
	    {else}error{/if}
	  </td>

	  {$arr_location=","|explode:$kw.kw_location_name}
	  <td class='jqPlot-show{$cnt}' title="{$kw.kw_location_name}">{$arr_location[0]}</td><!--マウスオーバーでロケーション情報全部表示 / ロケーション情報は末端のみ使用-->
	  <td class='jqPlot-show{$cnt}'>{$comp_today{$gp1}}</td>
	  <td class='jqPlot-show{$cnt}'><span class={if $comp_yesterday{$gp1}>0}up{elseif $comp_yesterday{$gp1}<0}down{else}same{/if}>{if $comp_yesterday{$gp1}>0}↑{elseif $comp_yesterday{$gp1}<0}↓{else}{/if}{$comp_yesterday{$gp1}}</span></td>
	  <td class='jqPlot-show{$cnt}'><span class={if $comp_week{$gp1}>0}up{elseif $comp_week{$gp1}<0}down{else}same{/if}>{if $comp_week{$gp1}>0}↑{elseif $comp_week{$gp1}<0}↓{else}{/if}{$comp_week{$gp1}}</span></td>
	  <td class='jqPlot-show{$cnt}'><span class={if $comp_month{$gp1}>0}up{elseif $comp_month{$gp1}<0}down{else}same{/if}>{if $comp_month{$gp1}>0}↑{elseif $comp_month{$gp1}<0}↓{else}{/if}{$comp_month{$gp1}}</span></td>
	  <td class='jqPlot-show{$cnt}'>{$comp_today{$gp2}}</td>
	  <td class='jqPlot-show{$cnt}'><span class={if $comp_yesterday{$gp2}>0}up{elseif $comp_yesterday{$gp2}<0}down{else}same{/if}>{if $comp_yesterday{$gp2}>0}↑{elseif $comp_yesterday{$gp2}<0}↓{else}{/if}{$comp_yesterday{$gp2}}</span></td>
	  <td class='jqPlot-show{$cnt}'>{$comp_today{$gp3}}</td>
	  <td class='jqPlot-show{$cnt}'><span class={if $comp_yesterday{$gp3}>0}up{elseif $comp_yesterday{$gp3}<0}down{else}same{/if}>{if $comp_yesterday{$gp3}>0}↑{elseif $comp_yesterday{$gp3}<0}↓{else}{/if}{$comp_yesterday{$gp3}}</span></td>
	</tr>

    <tr>
	  <td colspan=15 class="jqPlot-area{$cnt}">
	    <div id="result_jqPlot{$cnt}">
      </td>
      {* Graph *}
      {*include file="../../../../../public/js/my/top_jqplot_gp.php"*}
      {* slideDown *}
      {include file="../../../../../public/js/my/top_jqplot_show.php"}
    </tr>




	{$cnt=$cnt+1}

	{foreachelse}
      キーワード情報はありませんでした。
	{/foreach}



	</table>

    {* checkboxクリック動作 ＆ キーワード更新ボタン時の操作 *}
    {include file="../../../../../public/js/my/top_chkbox.php"}

    {* ウォッチリスト追加 & 削除時の操作 *}
    {include file="../../../../../public/js/my/top_watchlist.php"}
