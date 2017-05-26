{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

  <script src="{base_url()}../../js/my/fmsubmit.js"></script>

<div id="contents" class="container">

<h4>【グループ内検索】</h4>
{form_open('/grouplist/detail_search/' , 'name="searchForm" class="form-horizontal"')}
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">キーワード</td>
        <td class="col-md-2">
          {form_input('kw_keyword' , set_value('kw_keyword', {$seach_keyword}) , 'class="form-control" placeholder="キーワードを入力してください。"')}
          {if form_error('kw_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_keyword')}</font></label>{/if}
        </td>
        <td class="col-md-1">ドメイン名</td>
        <td class="col-md-3">
          {form_input('kw_domain' , set_value('kw_domain', {$seach_domain}) , 'class="form-control" placeholder="ドメイン名を入力してください。"')}
          {if form_error('kw_domain')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_domain')}</font></label>{/if}
        </td>
        <td class="col-md-1">ID並び替え</td>
        <td class="col-md-2">
          {form_dropdown('orderid', $options_orderid, set_value('orderid', {$seach_orderid}))}
        </td>
      </tr>
    </tbody>
  </table>

  {form_hidden('gt_seq', $gt_seq)}

  <div class="row">
    <div class="col-md-5 col-md-offset-5">
      {$attr['name']  = 'submit'}
      {$attr['type']  = 'submit'}
      {$attr['value'] = '_submit'}
      {form_button($attr , '検　　索' , 'class="btn btn-default"')}
    </div>
  </div>

{form_close()}

<ul class="pagination pagination-sm">
    検索結果： {$countall}件<br />
    {$set_pagination}
</ul>

<p>グループ名：{$gt_name}<br>表示期間：{$end_date} ～ {$start_date} (31日間)</p>
{form_open('/grouplist/detail_search/' , 'name="detailForm" class="form-horizontal"')}

  {foreach from=$list item=kw}

    <div class="row">
      <div class="col-md-1">
        {if $kw.kw_status == "1"}<span class="label label-primary">有効</span>
        {elseif $kw.kw_status == "0"}<span class="label label-default">無効</span>
        {else}エラー
        {/if}
     </div>
     <div class="col-md-11 text-right">
        <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/keyworddetail/detail/', 'POST', '{$kw.kw_seq}', 'chg_seq');">詳　細</button>
        {if $smarty.session.c_memKw==1}
          <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/keywordlist/chg/', 'POST', '{$kw.kw_seq}', 'chg_seq');">編　集</button>
        {/if}
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
          <td>
            rank
          </td>
          {$comp_y_data=0}
          {foreach from=$tbl_y_data{$kw.kw_seq} item=y_data}
            <td class="text-right">
              {if $y_data<$comp_y_data}<font color="#ff0000">{$y_data}</font>{else}{$y_data}{/if}
            </td>
            {$comp_y_data=$y_data}
          {/foreach}
        </tr>
      </tbody>

    </table>

  {foreachelse}
    検索結果はありませんでした。
  {/foreach}

  {form_hidden('gt_seq', $gt_seq)}

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
