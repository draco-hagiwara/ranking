{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link href="{base_url()}../../css/my/top.css" rel="stylesheet">

<body>
{* ヘッダー部分　END *}

  <script type="text/javascript" src="{base_url()}../../js/my/fmsubmit.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jquery.sparkline.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/my/toggleslide.js"></script>

<div id="contents" class="container">

<h5>【 キーワード情報　検索 】</h5>
{form_open('/top/search/' , 'name="searchForm" class="form-horizontal"')}


  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">キーワード</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_keyword' , set_value('kw_keyword', {$seach_kw_keyword}) , 'class="form-control"')}
          {if form_error('kw_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_keyword')}</font></label>{/if}
        </td>
        <td class="col-md-1">対象URL</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_domain' , set_value('kw_domain', {$seach_kw_domain}) , 'class="form-control"')}
          {if form_error('kw_domain')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_domain')}</font></label>{/if}
        </td>
      </tr>
    </tbody>
  </table>

  <dl id="acMenu">
  <dt>
    &emsp;&emsp;詳細検索
  </dt>

  <dd>
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">グループ名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_group' , set_value('kw_group', {$seach_kw_group}) , 'class="form-control"')}
          {if form_error('kw_group')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_group')}</font></label>{/if}
        </td>
        <td class="col-md-1">タグ名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_tag' , set_value('kw_tag', {$seach_kw_tag}) , 'class="form-control"')}
          {if form_error('kw_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_tag')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_status', $options_kw_status, set_value('kw_status', {$seach_status}))}
        </td>
        <td class="col-md-1">ID並び替え</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('orderid', $options_orderid, set_value('orderid', {$seach_orderid}))}
        </td>
        <td class="col-md-1">ｳｫｯﾁﾘｽﾄ</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('watchlist', $options_watchlist, set_value('watchlist', {$seach_watchlist}))}
        </td>
        <td class="col-md-1">担当者</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_ac_seq', $options_accountlist, set_value('accountlist', {$seach_accountlist}))}
        </td>
      </tr>
    </tbody>
  </table>
  </dd>
  </dl>

  <div class="row">
    <div class="col-md-5 col-md-offset-5">
      {$attr['name']  = 'submit'}
      {$attr['type']  = 'submit'}
      {$attr['value'] = '_submit'}
      {form_button($attr , '検&emsp;&emsp;索' , 'class="btn btn-default btn-md"')}
    </div>
  </div>

{form_close()}

<ul class="pagination pagination-sm">
  検索結果： {$countall}件<br />
  {$set_pagination}
</ul>

<p>ランク表示期間：{$end_date} ～ {$start_date} (31日間)</p>
{form_open('/top/detail/' , 'name="detailForm" class="form-horizontal"')}

  {foreach from=$list item=kw}
  <div class="row">
    <div class="col-md-1">
      {if $kw.kw_status == "1"}<span class="label label-primary">有効</span>
      {elseif $kw.kw_status == "0"}<span class="label label-default">無効</span>
      {else}エラー
      {/if}
    </div>
    <div class="col-md-11 text-right">
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/detail/0/', 'POST', '{$kw.kw_seq}', 'chg_seq');">1ヶ月</button>
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/detail/1/', 'POST', '{$kw.kw_seq}', 'chg_seq');">3ヶ月</button>
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/detail/2/', 'POST', '{$kw.kw_seq}', 'chg_seq');">6ヶ月</button>
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/detail/3/', 'POST', '{$kw.kw_seq}', 'chg_seq');">1週間</button>
      <button type="button" class="btn {if $kw.wt_seq}btn-warning{else}btn-success{/if} btn-xs" onclick="fmSubmit('detailForm', '/client/top/watchlist/', 'POST', '{$kw.kw_seq}', 'chg_seq');">★ウォッチ</button>
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/report/', 'POST', '{$kw.kw_seq}', 'chg_seq');">report</button>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      ID:{$kw.kw_seq}
      , 【{$kw.kw_keyword}】
      , {if $kw.kw_matchtype==0}完全一致{elseif $kw.kw_matchtype==1}前方一致{elseif $kw.kw_matchtype==2}ドメイン一致{elseif $kw.kw_matchtype==3}ルートドメイン一致{else}error{/if}
      , {if $kw.kw_searchengine==0}<font color="#0000ff">Google</font>{elseif $kw.kw_searchengine==1}<font color="#dc143c">Yahoo!</font>{else}error{/if}
      , {if $kw.kw_device==0}PC{elseif $kw.kw_device==1}Mobile{else}error{/if}
      , 【{$kw.kw_location_name}】
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 textOverflow">
      {$kw.kw_url}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-11">

    <table class="table table-striped table-hover table-condensed">
      <thead>
        <tr>
          <th class="text-center">日付</th>
          {foreach from=$tbl_x_data{$kw.kw_seq} item=head}
            <th class="text-center">{$head}</th>
          {/foreach}
          <th></th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>rank</td>
          {$comp_y_data=0}
          {foreach from=$tbl_y_data{$kw.kw_seq} item=y_data}
            <td class="text-right">
              {if $y_data < $comp_y_data}<font color="#ff0000">{$y_data}</font>{else}{$y_data}{/if}
            </td>
            {$comp_y_data=$y_data}
          {/foreach}
        </tr>
      </tbody>
    </table>

    </div>
    <div class="col-md-1">
      <span id="sparkline{$kw.kw_seq}">Loading..</span>
    </div>
</div>

<script type="text/javascript">
  $("#sparkline{$kw.kw_seq}").sparkline([{$y_min_data{$kw.kw_seq}}], {
	    type: 'line',
	    width: '70',
	    height: '60',
	    spotColor: '#',
	    chartRangeMin: 200,
	    chartRangeMax: 300
  });
</script>

  {foreachelse}
    検索結果はありませんでした。
  {/foreach}

{form_close()}


<ul class="pagination pagination-sm">
  {$set_pagination}
</ul>

</div>

<script type="text/javascript">
  $('.sparklines').sparkline('html');
</script>


{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
