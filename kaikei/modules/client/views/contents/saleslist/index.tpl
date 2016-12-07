{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<div id="contents" class="container">

<h5>【 売上データ情報　検索 】</h5>
{form_open('/saleslist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">請求書NO</td>
        <td class="col-md-2 input-group-sm">
          {form_input('sa_slip_no' , set_value('sa_slip_no', {$seach_sa_slip_no}) , 'class="form-control"')}
          {if form_error('sa_slip_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sa_slip_no')}</font></label>{/if}
        </td>
        <td class="col-md-1">顧客CD</td>
        <td class="col-md-2 input-group-sm">
          {form_input('sa_cm_seq' , set_value('sa_cm_seq', {$seach_sa_cm_seq}) , 'class="form-control"')}
          {if form_error('sa_cm_seq')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sa_cm_seq')}</font></label>{/if}
        </td>
        <td class="col-md-1">会 社 名</td>
        <td class="col-md-2 input-group-sm">
          {form_input('sa_company' , set_value('sa_company', {$seach_sa_company}) , 'class="form-control"')}
          {if form_error('sa_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sa_company')}</font></label>{/if}
        </td>
        <td class="col-md-1">担当営業</td>
        <td class="col-md-2 input-group-sm">
          {form_input('sa_salesman' , set_value('sa_salesman', {$seach_sa_salesman}) , 'class="form-control"')}
          {if form_error('sa_salesman')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sa_salesman')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">売上日<font color=red> *</font></td>
        <td class="col-md-2 input-group-sm">
          {form_input('sa_sales_date01' , set_value('sa_sales_date01', {$seach_sa_sales_date01}) , 'class="form-control"')}
          {if form_error('sa_sales_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sa_sales_date01')}</font></label>{/if}
        </td>
        <td class="col-md-1 text-center">　～　</td>
        <td class="col-md-2 input-group-sm">
          {form_input('sa_sales_date02' , set_value('sa_sales_date02', {$seach_sa_sales_date02}) , 'class="form-control"')}
          {if form_error('sa_sales_date02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sa_sales_date02')}</font></label>{/if}
        </td>
        <td class="col-md-1">回収サイト</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('sa_collect', $options_sa_collect, set_value('sa_collect', {$seach_sa_collect}))}
        </td>
        <td class="col-md-1">表示方法</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('displine', $options_displine, set_value('displine', {$seach_displine}))}
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
        <th>売 上 日</th>
        <th>会 社 名</th>
        <th>請求書NO</th>
        <th>売上金額</th>
        <th>担当営業</th>
      </tr>
    </thead>

    {foreach from=$list item=sa name="seq"}
      <tbody>
        <tr>
          <td>
            {$sa.sa_sales_date}
          </td>
          <td>
            {$sa.sa_company}
          </td>
          <td>
            {$sa.sa_slip_no}
          </td>
          <td>
            {$sa.sa_total|number_format} 円
          </td>
          <td>
            {$sa.sa_salesman}
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
        <th>売 上 日</th>
        <th>売上金額</th>
      </tr>
    </thead>

    {foreach from=$list item=sa name="seq"}
      <tbody>
        <tr>
          <td>
            {$sa.sa_sales_date}
          </td>
          <td>
            {$sa.sum_total|number_format} 円
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
        <th>会 社 名</th>
        <th>売上金額</th>
        <th>担当営業</th>
      </tr>
    </thead>

    {foreach from=$list item=sa name="seq"}
      <tbody>
        <tr>
          <td>
            {$sa.sa_company}
          </td>
          <td>
            {$sa.sum_total|number_format} 円
          </td>
          <td>
            {$sa.sa_salesman}
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>

  {elseif $seach_displine==3}
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>担当営業</th>
        <th>売上金額</th>
      </tr>
    </thead>

    {foreach from=$list item=sa name="seq"}
      <tbody>
        <tr>
          <td>
            {$sa.sa_salesman}
          </td>
          <td>
            {$sa.sum_total|number_format} 円
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>

  {/if}



{form_open('/saleslist/csvdown/' , 'name="detailForm" class="form-horizontal"')}

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
