{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

  <script src="{base_url()}../../js/my/fmsubmit.js"></script>

<div id="contents" class="container">

<h4>【タグ検索】</h4>
{form_open('/taglist/search/' , 'name="searchForm" class="form-horizontal"')}
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">タグ名</td>
        <td class="col-md-4">
          {form_input('gt_name' , set_value('gt_name', {$seach_gtname}) , 'class="form-control" placeholder="タグ名を入力してください。"')}
          {if form_error('gt_name')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('gt_name')}</font></label>{/if}
        </td>
        <td class="col-md-1">ID並び替え</td>
        <td class="col-md-2">
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
      {form_button($attr , '検　　索' , 'class="btn btn-default"')}
    </div>
  </div>

{form_close()}

<ul class="pagination pagination-sm">
    検索結果： {$countall}件<br />
    {$set_pagination}
</ul>

{form_open('/taglist/detail/' , 'name="detailForm" class="form-horizontal"')}

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-1">ID</th>
                <th>タグ名</th>
                <th class="col-md-1 text-center">rootdomain</th>
                <th class="col-md-1 text-center">keyword</th>
                <th class="col-md-1"></th>
            </tr>
        </thead>


        {foreach from=$list item=gt}
        <tbody>
            <tr>
                <td>
                    {$gt.gt_seq}
                </td>
                <td>
                    {$gt.gt_name}
                </td>
                <td class="text-center">
                    {$gt.gt_domain_cnt|number_format}
                </td>
                <td class="text-center">
                    {$gt.gt_keyword_cnt|number_format}
                </td>
                <td>
                    <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/taglist/detail/', 'POST', '{$gt.gt_seq}', 'chg_gtseq');">順位データ一覧</button>
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
