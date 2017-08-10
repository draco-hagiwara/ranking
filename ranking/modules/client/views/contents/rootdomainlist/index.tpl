{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

  <script src="{base_url()}../../js/my/fmsubmit.js"></script>

<div id="contents" class="container">

<h5>【 ルートドメイン管理情報　検索 】</h5>
{form_open('/rootdomainlist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">ﾙｰﾄﾄﾞﾒｲﾝ</td>
        <td colspan="3" class="col-md-5 input-group-md">
          {form_input('rd_rootdomain' , set_value('rd_rootdomain', {$seach_rd_rootdomain}) , 'class="form-control"')}
          {if form_error('rd_rootdomain')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rd_rootdomain')}</font></label>{/if}
        </td>
        <td class="col-md-1">サイト名</td>
        <td class="colspan="3" class="col-md-5 input-group-md">
          {form_input('rd_sitename' , set_value('rd_sitename', {$seach_rd_sitename}) , 'class="form-control"')}
          {if form_error('rd_sitename')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rd_sitename')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">グループ名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('rd_group' , set_value('rd_group', {$seach_rd_group}) , 'class="form-control"')}
          {if form_error('rd_group')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rd_group')}</font></label>{/if}
        </td>
        <td class="col-md-1">タグ名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('rd_tag' , set_value('rd_tag', {$seach_rd_tag}) , 'class="form-control"')}
          {if form_error('rd_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rd_tag')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">ID並び替え</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('orderid', $options_orderid, set_value('orderid', {$seach_orderid}))}
        </td>
        <td class="col-md-1">ｳｫｯﾁﾘｽﾄ</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('watchlist', $options_watchlist, set_value('watchlist', {$seach_watchlist}))}
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
  検索結果： {$countall}件<br />
  {$set_pagination}
</ul>

{form_open('/rootdomainlist/chg/' , 'name="detailForm" class="form-horizontal"')}

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>ルートドメイン</th>
        <th>サイト名</th>
        <th class="col-md-1 text-center">KW数</th>
        <th class="col-md-2"></th>
      </tr>
    </thead>

    {foreach from=$list item=rd}
    <tbody>
      <tr>
        <td>
          {$rd.rd_seq}
        </td>
        <td class="text-left">
          {$rd.rd_rootdomain}
        </td>
        <td class="text-left">
          {$rd.rd_sitename}
        </td>
        <td class="text-center">
          {$rd.rd_keyword_cnt|number_format}
        </td>
        <td class="text-right">
          <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/top/search/', 'POST', '{$rd.rd_rootdomain}', 'sel_rdname');">一 覧</button>
          <button type="button" class="btn {if $rd.wt_ac_seq==$smarty.session.c_memSeq}btn-warning{else}btn-success{/if} btn-xs" onclick="fmSubmit('detailForm', '/client/rootdomainlist/watchlist/', 'POST', '{$rd.rd_seq}', 'chg_seq');">★ウォッチ</button>
          <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/rootdomainlist/chg/', 'POST', '{$rd.rd_seq}', 'chg_seq');">編 集</button>
        </td>
      </tr>
    </tbody>
    {foreachelse}
      検索結果はありませんでした。
    {/foreach}

  </table>

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
