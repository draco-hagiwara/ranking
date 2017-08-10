{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link href="{base_url()}../../css/my/keywordlist.css" rel="stylesheet">

<style>
.popover {
    max-width: 500px;
}
</style>


<body>
{* ヘッダー部分　END *}

  <script src="{base_url()}../../js/my/fmsubmit.js"></script>
  {* <script src="{base_url()}../../js/my/toggleslide.js"></script> *}

{* POP-Over *}
<script src="{base_url()}../../js/my/popover.js"></script>

<script>
$(function(){
    $("#kwMenu dt").on("click", function() {
        $(this).next().slideToggle();
        $(this).toggleClass("active");//追加部分
    });
});
</script>

<div id="contents" class="container">

<h5>【 キーワード管理情報　検索 】</h5>
{form_open('/keywordlist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">キーワード</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_keyword' , set_value('kw_keyword', {$seach_kw_keyword}) , 'class="form-control"')}
          {if form_error('kw_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_keyword')}</font></label>{/if}
        </td>
        <td class="col-md-1">ﾙｰﾄﾄﾞﾒｲﾝ</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_domain' , set_value('kw_domain', {$seach_kw_domain}) , 'class="form-control"')}
          {if form_error('kw_domain')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_domain')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_status', $options_kw_status, set_value('kw_status', {$seach_kw_status}))}
        </td>
        <td class="col-md-1">並び替え</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('orderid', $options_orderid, set_value('orderid', {$seach_orderid}))}
        </td>
      </tr>
    </tbody>
  </table>

  <div class="row">
    <div class="col-md-5 col-md-offset-5">
      {$attr['name']  = 'submit'}
      {$attr['type']  = 'submit'}
      {$attr['value'] = '_submit'}
      {form_button($attr , '検　　索' , 'class="btn btn-default btn-md"')}
    </div>
  </div>

{form_close()}

<ul class="pagination pagination-sm">
  ﾙｰﾄﾄﾞﾒｲﾝ検索結果： {$countall}件<br />
  {$set_pagination}
</ul>

<p>表示期間：{$end_date} ～ {$start_date} (31日間)</p>
{form_open('/keywordlist/detail/' , 'name="detailForm" class="form-horizontal"')}


  <dl id="kwMenu">

  {$old_rootdomain = ""}
  {foreach from=$list item=kw}
  {if $old_rootdomain != $kw.kw_rootdomain}
    {$old_rootdomain = $kw.kw_rootdomain}
    </dd><dt style="font-size:16px;color:#000000;">&emsp; {$kw.kw_rootdomain}</dt><dd>
  {/if}
    <div class="row">
      <div class="col-md-10">
        {if $kw.kw_status == "1"}<span class="label label-primary">有効</span>
        {elseif $kw.kw_status == "0"}<span class="label label-default">無効</span>
        {else}エラー
        {/if}
        {if $kw.kw_searchengine==0}<span class="label" style="background-color:#0000ff;">Google</span>{elseif $kw.kw_searchengine==1}<span class="label" style="background-color:#dc143c;">Yahoo!</span>{else}error{/if}
        ID:{$kw.kw_seq} ,【{$kw.kw_keyword}】
        <button type="button" class="btn btn-default  btn-xs" data-container="body" data-toggle="popover" data-placement="right"
          data-content="<b>【ルートドメイン】</b>{$kw.kw_rootdomain}<br>
                        <b>【URL一致方式】</b>{if $kw.kw_matchtype==0}完全一致{elseif $kw.kw_matchtype==1}前方一致{elseif $kw.kw_matchtype==2}ドメイン一致{elseif $kw.kw_matchtype==3}ルートドメイン一致{else}error{/if}<br>
                        <b>【デバイス】</b>{if $kw.kw_device==0}PC{elseif $kw.kw_device==1}Mobile{else}error{/if}<br>
                        <b>【ロケーション】</b>{$kw.kw_location_name}<br>
                        <b>【最大取得順位】</b>{if $kw.kw_maxposition==0}100件{elseif $kw.kw_maxposition==1}200件{elseif $kw.kw_maxposition==2}300件{else}error{/if}<br>
                        <b>【データ取得回数】</b>{if $kw.kw_trytimes==0}1回{elseif $kw.kw_trytimes==1}2回{elseif $kw.kw_trytimes==2}3回{else}error{/if}<br>
                        <b>【設定グループ】</b>{$kw.kw_group}<br>
                        <b>【設定タグ】</b>{$kw.kw_tag}<br>
                        <b>【メモ】</b>{if isset($me_list[$kw.kw_seq])}{foreach from=$me_list[$kw.kw_seq] item=me}<tbody><tr><td><br>{$me}</td></tr></tbody>{/foreach}{/if}<br>
                       ">
          詳細表示
        </button>
      </div>
     <div class="col-md-2 text-right">
        {*
        <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/keyworddetail/detail/', 'POST', '{$kw.kw_seq}', 'chg_seq');">詳　細</button>
        <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/keyworddetail/report/', 'POST', '{$kw.kw_seq}', 'chg_seq');">report</button>
        <button type="button" class="btn {if $kw.wt_seq}btn-warning{else}btn-success{/if} btn-xs" onclick="fmSubmit('detailForm', '/client/keywordlist/watchlist/', 'POST', '{$kw.kw_seq}', 'chg_seq');">☆ウォッチ</button>
        *}
        {if $smarty.session.c_memKw==1}
          <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/keywordlist/chg/', 'POST', '{$kw.kw_seq}', 'chg_seq');">編　集</button>
        {/if}
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 textOverflow">
        {$kw.kw_url}
      </div>
    </div>

    <table class="table table-striped table-hover table-condensed">
      <thead>
        <tr>
          <th class="text-center">日付</th>
          {foreach from=$tbl_x_data{$kw.kw_seq} item=head}
            <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">
            rank
          </td>
          {foreach from=$tbl_y_data{$kw.kw_seq} item=y_data}
            <td class="text-center">
              <small>{$y_data}</small>
            </td>
          {/foreach}
        </tr>
      </tbody>

    </table>


  {foreachelse}
    検索結果はありませんでした。
  {/foreach}

  </dl>

{form_close()}


{form_open('/data_csv/kwlist_csvdown/' , 'name="detailForm" class="form-horizontal"')}

  <div class="row">
    <div class="col-sm-2">
      <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal01">↓　ダウンロード</button>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">キーワード管理情報のCSVダウンロード</h4>
        </div>
        <div class="modal-body">
          <p>ダウンロードしますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}


<ul class="pagination pagination-sm">
  {$set_pagination}
</ul>

</div>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
