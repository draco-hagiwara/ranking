
{$gp1=$cnt|cat:"00"}{* google-pc *}
{$gp2=$cnt|cat:"01"}{* google-mobile *}
{$gp3=$cnt|cat:"10"}{* yahoo!-pc *}

<div id="jqPlot-targetPlot{$cnt}" class="col-md-9" style="height: 200px; width: 530px;"></div>

<div class="col-md-3" style="height: 210px; width: 250px;overflow:auto;">

  <table>
	<tr style="background-color: #bde9ba;">
	  <th>取得日</th>
	  <th>G-pc</th>
	  <th>G-mo</th>
	  <th>Y-pc</th>
	</tr>

	{$i=$tbl_x_data000|@count-1}
	{foreach from=$tbl_x_data000|@array_reverse:true item=kw name="cnt"}{* 降順で表示 *}
	<tr>
	  <th>{$kw}</th>
	  <th>{if $tbl_y_data000[{$i}]}{$tbl_y_data000[{$i}]}{else}-{/if}</th>
	  <th>{if $tbl_y_data001[{$i}]}{$tbl_y_data001[{$i}]}{else}-{/if}</th>
	  <th>{if $tbl_y_data010[{$i}]}{$tbl_y_data010[{$i}]}{else}-{/if}</th>
	</tr>
	{$i=$i-1}
	{foreachelse}
      情報はありませんでした。
	{/foreach}

  </table>
<div>



{* Graph *}
{include file="../../../../../public/js/my/top_jqplot_gp.php"}
