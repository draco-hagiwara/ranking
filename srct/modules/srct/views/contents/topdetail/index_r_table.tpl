{*** 右サイド ***}

<table class="rank_tb">

  <thead class="scrollHead">

		<tr><!--各テーブルヘッダはクリックするとソートする。-->
	  	  <th class="t_ck"><input type="checkbox" id="checkbox_all" /></th><!--一括チェック＆解除。チェックしたものが処理対象になる。-->
	  	  <th id="wl_sort_id" class="t_wk wl_sort_cl"></th><!--一括ウォッチリストではない。単なるソート-->
	  	  <th class="t_gr group_sort_cl" id="group_sort_id">グループ</th>
	  	  <th class="t_kw keyword_sort_cl" id="keyword_sort_id">キーワード</th>
	  	  <th colspan="2" class="t_ur url_sort_cl" id="url_sort_id"><!--URLは一定文字数以上は省略する。30文字程度？-->
			<p class="f_l no_space">URL</p>
			<p class="f_r no_space">マッチタイプ</p>
	  	  </th>
	  	  <th class="t_lo location_sort_cl" id="location_sort_id">地域</th>
	  	  <th colspan="4" class="t_gp gpc_sort_cl" id="gpc_sort_id"></th>
	  	  <th colspan="2" class="t_gm gmo_sort_cl" id="gmo_sort_id"></th>
	  	  <th colspan="2" class="t_yp ypc_sort_cl" id="ypc_sort_id"></th>
	  	  {*
	  	  <th colspan="4" class="t_gp gpc_sort_cl" id="gpc_sort_id"><img src="/images/user/monitor.png" width="16" /><img src="/images/user/google_favicon.png" /></th>
	  	  <th colspan="2" class="t_gm gmo_sort_cl" id="gmo_sort_id"><img src="/images/user/smartphone.png" width="16" /><img src="/images/user/google_favicon.png" /></th>
	  	  <th colspan="2" class="t_yp ypc_sort_cl" id="ypc_sort_id"><img src="/images/user/monitor.png" width="16" style="margin-right: 2px;" /><img src="/images/user/yahoo_favicon.png" /></th>
		  *}
		</tr>
  </thead>

  <tbody class="scrollBody bottom_rank_tb" id="bottom_rank_tb">

	{$cnt=0}
	{foreach from=$list_kw item=kw name="cnt"}

	  {$gp1=$cnt|cat:"00"}{* google-pc *}
      {$gp2=$cnt|cat:"01"}{* google-mobile *}
      {$gp3=$cnt|cat:"10"}{* yahoo!-pc *}


	  <tr>
	    <td class="checkbox_kw{$cnt} t_ck"><input type="checkbox" name="checkbox_kwseq[]" value="{$kw.kw_seq}" /></td>
	    <td>
	      <label class="btn_wk {if $kw.wt_seq}active{/if}" data-text-default="☆" data-text-clicked="★" data-kw-seq={$kw.kw_seq}>{if $kw.wt_seq}<img id="watchlist_kw{$kw.kw_seq}" src="/images/user/wl_on.png" />{else}<img id="watchlist_kw{$kw.kw_seq}" src="/images/user/wl.png" />{/if}</label>
	    </td>
	    <td class="jqPlot-show t_gr" id="{$kw.kw_seq}">{if $kw.kw_group}{$kw.kw_group}{else}-{/if}</td>
	    <td class="jqPlot-show t_kw" id="{$kw.kw_seq}">{$kw.kw_keyword}</td>
	    <td class="f_ur"><a href="{$kw.kw_url}" target="_blank">{$kw.kw_url}</a></td>
	    <td class='jqPlot-show f_mt' id="{$kw.kw_seq}">
	      {if {$kw.kw_matchtype==0}}<img src="/images/user/p1.png" title="完全一致" />
	      {elseif {$kw.kw_matchtype==1}}<img src="/images/user/p2.png" title="前方一致" />
	      {elseif {$kw.kw_matchtype==2}}<img src="/images/user/p3.png" title="ドメイン一致" />
	      {elseif {$kw.kw_matchtype==3}}<img src="/images/user/p4.png" title="ルートドメイン一致" />
	      {else}error{/if}
	    </td>

	    {$arr_location=" "|explode:$kw.kw_location_name}{$arr_location=","|explode:$arr_location[0]}
	    <td class='jqPlot-show t_lo' id="{$kw.kw_seq}" title="{$kw.kw_location_name}">{$arr_location[0]}</td><!--マウスオーバーでロケーション情報全部表示 / ロケーション情報は末端のみ使用-->

	    <td class='jqPlot-show f_rk' id="{$kw.kw_seq}">{$comp_today{$gp1}}</td>
	    <td class='jqPlot-show f_po' id="{$kw.kw_seq}"><span class={if $comp_yesterday{$gp1}>0}up{elseif $comp_yesterday{$gp1}<0}down{else}same{/if}>{if $comp_yesterday{$gp1}>0}↑{elseif $comp_yesterday{$gp1}<0}↓{else}{/if}{$comp_yesterday{$gp1}}</span></td>
	    <td class='jqPlot-show f_po' id="{$kw.kw_seq}"><span class={if $comp_week{$gp1}>0}up{elseif $comp_week{$gp1}<0}down{else}same{/if}>{if $comp_week{$gp1}>0}↑{elseif $comp_week{$gp1}<0}↓{else}{/if}{$comp_week{$gp1}}</span></td>
	    <td class='jqPlot-show f_po' id="{$kw.kw_seq}"><span class={if $comp_month{$gp1}>0}up{elseif $comp_month{$gp1}<0}down{else}same{/if}>{if $comp_month{$gp1}>0}↑{elseif $comp_month{$gp1}<0}↓{else}{/if}{$comp_month{$gp1}}</span></td>
	    <td class='jqPlot-show f_rk' id="{$kw.kw_seq}">{$comp_today{$gp2}}</td>
	    <td class='jqPlot-show f_po' id="{$kw.kw_seq}"><span class={if $comp_yesterday{$gp2}>0}up{elseif $comp_yesterday{$gp2}<0}down{else}same{/if}>{if $comp_yesterday{$gp2}>0}↑{elseif $comp_yesterday{$gp2}<0}↓{else}{/if}{$comp_yesterday{$gp2}}</span></td>
	    <td class='jqPlot-show f_rk' id="{$kw.kw_seq}">{$comp_today{$gp3}}</td>
	    <td class='jqPlot-show f_po' id="{$kw.kw_seq}"><span class={if $comp_yesterday{$gp3}>0}up{elseif $comp_yesterday{$gp3}<0}down{else}same{/if}>{if $comp_yesterday{$gp3}>0}↑{elseif $comp_yesterday{$gp3}<0}↓{else}{/if}{$comp_yesterday{$gp3}}</span></td>

	  </tr>
      <tr>
	    <td colspan=15 class="jqPlot-area{$cnt}" style="display:none;">
	      <div id="result_jqPlot{$cnt}">
        </td>
      </tr>
	  {$cnt=$cnt+1}

	{foreachelse}
      <tr><td>キーワード情報はありませんでした。</td></tr>
	{/foreach}

  </tbody>
</table>

{* テーブルヘッダをマウスクリックによる昇順降順並替 *}
{include file="../../../../../public/js/my/top_sort.php"}

{* checkboxクリック動作 ＆ キーワード更新ボタン時の操作 *}
{include file="../../../../../public/js/my/top_chkbox.php"}

{* ウォッチリスト追加 & 削除時の操作 *}
{include file="../../../../../public/js/my/top_watchlist.php"}

{* slideDown *}
{include file="../../../../../public/js/my/top_jqplot_show.php"}

{* 順次読込 : bottom *}
{if isset($per_page) && $per_page!=0}
{include file="../../../../../public/js/my/top_bottom.php"}
{/if}


<script>
$(function () {

	$('.kwcnt_rank_tb').text("KW件数： {$list_cnt}件");

	$("#wl_sort_id").text("");
	$("#group_sort_id").text("グループ");
	$("#keyword_sort_id").text("キーワード");
	$("#url_sort_id").text("URL　　　　　　マッチタイプ");
	$("#location_sort_id").text("地域");
	$("#gpc_sort_id").empty();
	$("#gpc_sort_id").append('<img src="/images/user/monitor.png" width="16" /><img src="/images/user/google_favicon.png" />');
	$("#gmo_sort_id").empty();
	$("#gmo_sort_id").append('<img src="/images/user/smartphone.png" width="16" /><img src="/images/user/google_favicon.png" />');
	$("#ypc_sort_id").empty();
	$("#ypc_sort_id").append('<img src="/images/user/monitor.png" width="16" style="margin-right: 2px;" /><img src="/images/user/yahoo_favicon.png" />');

	if (("{$tmp_item}" == 'wl') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.wl_sort_cl').toggleClass('active');
        $("#wl_sort_id").append("▼");
	} else if (("{$tmp_item}" == 'wl') && ("{$tmp_sort}" == 'ASC')) {
        $("#wl_sort_id").append("▲");
	}

	if (("{$tmp_item}" == 'group') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.group_sort_cl').toggleClass('active');
        $("#group_sort_id").text("グループ ▼");
	} else if (("{$tmp_item}" == 'group') && ("{$tmp_sort}" == 'ASC')) {
        $("#group_sort_id").text("グループ ▲");
	}

	if (("{$tmp_item}" == 'keyword') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.keyword_sort_cl').toggleClass('active');
        $("#keyword_sort_id").text("キーワード ▼");
	} else if (("{$tmp_item}" == 'keyword') && ("{$tmp_sort}" == 'ASC')) {
        $("#keyword_sort_id").text("キーワード ▲");
	}

	if (("{$tmp_item}" == 'url') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.url_sort_cl').toggleClass('active');
        $("#url_sort_id").text("URL ▼　　　　　マッチタイプ");
	} else if (("{$tmp_item}" == 'url') && ("{$tmp_sort}" == 'ASC')) {
        $("#url_sort_id").text("URL ▲　　　　　マッチタイプ");
	}

	if (("{$tmp_item}" == 'location') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.location_sort_cl').toggleClass('active');
        $("#location_sort_id").text("地域 ▼");
	} else if (("{$tmp_item}" == 'location') && ("{$tmp_sort}" == 'ASC')) {
        $("#location_sort_id").text("地域 ▲");
	}

	if (("{$tmp_item}" == 'gpc') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.gpc_sort_cl').toggleClass('active');
        $("#gpc_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'gpc') && ("{$tmp_sort}" == 'ASC')) {
        $("#gpc_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'gmo') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.gmo_sort_cl').toggleClass('active');
        $("#gmo_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'gmo') && ("{$tmp_sort}" == 'ASC')) {
        $("#gmo_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'ypc') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.ypc_sort_cl').toggleClass('active');
        $("#ypc_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'ypc') && ("{$tmp_sort}" == 'ASC')) {
        $("#ypc_sort_id").append(" ▲");
	}

});

</script>