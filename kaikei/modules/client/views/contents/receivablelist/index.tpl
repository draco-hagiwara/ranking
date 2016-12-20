{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<div id="contents" class="container">

<h5>【 債権データ情報　検索 】</h5>
{form_open('/receivablelist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">顧客CD</td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_cm_seq' , set_value('rv_cm_seq', {$seach_rv_cm_seq}) , 'class="form-control"')}
          {if form_error('rv_cm_seq')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_cm_seq')}</font></label>{/if}
        </td>
        <td class="col-md-1">会 社 名</td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_company' , set_value('rv_company', {$seach_rv_company}) , 'class="form-control"')}
          {if form_error('rv_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_company')}</font></label>{/if}
        </td>
        <td class="col-md-1">担当営業</td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_salesman' , set_value('rv_salesman', {$seach_rv_salesman}) , 'class="form-control"')}
          {if form_error('rv_salesman')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_salesman')}</font></label>{/if}
        </td>
        <td class="col-md-1">表示方法</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('displine', $options_displine, set_value('displine', {$seach_displine}))}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">金額範囲</td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_total01' , set_value('rv_total01', {$seach_rv_total01}) , 'class="form-control"')}
          {if form_error('rv_total01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_total01')}</font></label>{/if}
        </td>
        <td class="col-md-1 text-center">　～　</td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_total02' , set_value('rv_total02', {$seach_rv_total02}) , 'class="form-control"')}
          {if form_error('rv_total02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_total02')}</font></label>{/if}
        </td>
        <td class="col-md-1">更新日付<font color=red> *</font></td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_create_date01' , set_value('rv_create_date01', {$seach_rv_create_date01}) , 'class="form-control"')}
          {if form_error('rv_create_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_create_date01')}</font></label>{/if}
        </td>
        <td class="col-md-1 text-center">　～　</td>
        <td class="col-md-2 input-group-sm">
          {form_input('rv_create_date02' , set_value('rv_create_date02', {$seach_rv_create_date02}) , 'class="form-control"')}
          {if form_error('rv_create_date02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rv_create_date02')}</font></label>{/if}
        </td>
      </tr>
      <tr>
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
  検索結果： {$countall}件<br />
  {$set_pagination}
</ul>


  {if $seach_displine==0}
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>CD</th>
        <th>会 社 名</th>
        <th class="text-center">金    額</th>
        <th class="text-center">消 費 税</th>
        <th>担当営業</th>
        <th>更新日付</th>
      </tr>
    </thead>

    {foreach from=$list item=rv name="seq"}
      <tbody>
        <tr>
          <td>
            {$rv.rv_cm_seq}
          </td>
          <td>
            {$rv.rv_company}
          </td>
          <td class="text-right">
            {$rv.rv_total|number_format} 円
          </td>
          <td class="text-right">
            ({$rv.rv_tax|number_format} 円)
          </td>
          <td>
            {$rv.rv_salesman}
          </td>
          <td>
            {$rv.rv_create_date}
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>

  {elseif $seach_displine==1}
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>CD</th>
        <th>会 社 名</th>
        <th>売 上 日</th>
        <th>金    額</th>
        <th>消 費 税</th>
        <th>入金金額</th>
        <th>請求書番号</th>
        <th>担当営業</th>
        <th>メ    モ</th>
      </tr>
    </thead>

    {foreach from=$list item=rv name="seq"}
      <tbody>
        <tr>
          <td class="text-center">
            {$rv.rv_cm_seq}
          </td>
          <td>
            {$rv.rv_company}
          </td>
          <td>
            {$rv.rv_sales_date}
          </td>
          <td class="text-right">
            {$rv.rv_total|number_format} 円
          </td>
          <td class="text-right">
            ({$rv.rv_tax|number_format} 円)
          </td>
          <td class="text-right">
            {$rv.rv_receive_total|number_format} 円
          </td>
          <td>
            {$rv.rv_slip_no}
          </td>
          <td>
            {$rv.rv_salesman}
          </td>
          <td>
            {$rv.rv_memo}
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>

  {elseif $seach_displine==2}
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>入 金 日</th>
        <th>CD</th>
        <th>会 社 名</th>
        <th>入金金額</th>
        <th>口座情報</th>
      </tr>
    </thead>

    {foreach from=$list item=rv name="seq"}
      <tbody>
        <tr>
          <td>
            {$rv.rv_sales_date}
          </td>
          <td>
            {$rv.rv_cm_seq}
          </td>
          <td>
            {$rv.rv_company}
          </td>
          <td>
            {$rv.rv_receive_total|number_format} 円
          </td>
          <td>
            {$rv.rv_bank_info}
          </td>
        </tr>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>

  {/if}


{form_open('/receivablelist/csvdown/' , 'name="detailForm" class="form-horizontal"')}

  {form_hidden('csv_data', $list)}

  {if count($list) > 0}
    <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal">ダウンロード</button>（最大1,000件）

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">CSVファイル　作成</h4>
          </div>
          <div class="modal-body">
            <p>作成しますか。&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type='submit' name='submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
            <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  {/if}

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
