{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<script type="text/javascript">
<!--
function fmSubmit(formName, url, method, num) {
  var f1 = document.forms[formName];

  console.log(num);

  /* エレメント作成&データ設定&要素追加 */
  var e1 = document.createElement('input');
  e1.setAttribute('type', 'hidden');
  e1.setAttribute('name', 'chg_seq');
  e1.setAttribute('value', num);
  f1.appendChild(e1);

  /* サブミットするフォームを取得 */
  f1.method = method;                                   // method(GET or POST)を設定する
  f1.action = url;                                      // action(遷移先URL)を設定する
  f1.submit();                                          // submit する
  return true;
}
// -->
</script>

<div id="contents" class="container">

<h5>【 請求書情報　検索 】</h5>
{form_open('/invoicelist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">請求書NO</td>
        <td class="col-md-2 input-group-sm">
          {form_input('iv_slip_no' , set_value('iv_slip_no', {$seach_iv_slip_no}) , 'class="form-control"')}
          {if form_error('iv_slip_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_slip_no')}</font></label>{/if}
        </td>
        <td class="col-md-1">顧客CD</td>
        <td class="col-md-2 input-group-sm">
          {form_input('iv_cm_seq' , set_value('iv_cm_seq', {$seach_iv_cm_seq}) , 'class="form-control"')}
          {if form_error('iv_cm_seq')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_cm_seq')}</font></label>{/if}
        </td>
        <td class="col-md-1">会 社 名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('iv_company' , set_value('iv_company', {$seach_iv_company}) , 'class="form-control"')}
          {if form_error('iv_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_company')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('iv_status', $options_iv_status, set_value('iv_status', {$seach_iv_status}))}
        </td>
        <td class="col-md-1">発行年月</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('iv_issue_yymm', $options_date_fix, set_value('iv_issue_yymm', {$seach_iv_issue_yymm}))}
        </td>
        <td colspan="4" class="col-md-1"></td>
      </tr>
      <tr>
        <td class="col-md-1">会社CD並替</td>
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

{form_open('/pdf_create/pdf_invoice/' , 'name="detailForm" class="form-horizontal"')}
{*form_open('/invoicelist/pdf_invoice/' , 'name="pdfForm" class="form-horizontal" target="_blank"')*}

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>status</th>
        <th>請求書NO</th>
        <th>会 社 名</th>
        <th>請求金額</th>
        <th>発 行 日</th>
        <th>振込期日</th>
        <th>売上作成日</th>
        <th></th>
      </tr>
    </thead>

    {foreach from=$list item=iv name="seq"}
      <tbody>
        <tr>
          <td>
            <label>
            <input type="checkbox" name="invoice{$smarty.foreach.seq.iteration}" id="invoice" value="{$iv.iv_seq}" class="invoice">
            {if $iv.iv_status == "0"}<span class="label label-primary">未発行</span>
            {elseif $iv.iv_status == "1"}<span class="label label-default">発行済</span>
            {elseif $iv.iv_status == "9"}<span class="label label-default">ｷｬﾝｾﾙ</span>
            {else}エラー
            {/if}
            </label>
          </td>
          <td>
            {* 顧客番号の桁が増えたら検索で対応？ *}
            {if $iv.iv_slip_no|substr:7:2 == "KT"}<font color=blue>{$iv.iv_slip_no}</font>
            {elseif $iv.iv_slip_no|substr:7:2 == "SK"}<font color=deeppink>{$iv.iv_slip_no}</font>
            {else}<font color=green>{$iv.iv_slip_no}</font>
            {/if}
          </td>
          <td>
            {if $iv.iv_agency_flg==1}[代]{/if}
            {$iv.iv_company_cm}
          </td>
          <td>
            {if $iv.iv_total>=0}{$iv.iv_total|number_format} 円
            {else}<font color=red>{$iv.iv_total|number_format} 円</font>
            {/if}
          </td>
          <td>
            {$iv.iv_issue_date}
          </td>
          <td>
            {$iv.iv_pay_date}
          </td>
          <td class="text-center">
            {if $iv.iv_sales_date}{$iv.iv_sales_date}{else}-{/if}
          </td>
          <td class="text-right">
            <button type="button" class="btn btn-warning btn-xs" onclick="fmSubmit('detailForm', '/client/invo_create/invoice_iv/', 'POST', '{$iv.iv_seq}', 'chg_seq');">個別作成</button>
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/invoicelist/historychk/', 'POST', '{$iv.iv_seq}', 'chg_seq');">履　歴</button>
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/invoicelist/detail/', 'POST', '{$iv.iv_seq}', 'chg_seq');">編　集</button>
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>




  {form_hidden('iv_issue_yymm', $seach_iv_issue_yymm)}

<div class="row">
  <div class="col-md-3">
    <input type="checkbox" id="invoice_all" name="invoice_all">
    <label for="invoice_all"> 全てチェック　</label>
    <button type='submit' class="btn btn-warning btn-xs" name='_submit' value='submit'>請求書PDF作成</button>
  </div>

  {*
  <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal">請求書PDF作成</button>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">請求書PDF　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
          <button type='submit' name='_submit' value='cancel' class="btn btn-sm btn-primary">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  *}

{form_close()}

{form_open('/data_csvup/invoice_csvdown/' , 'name="csvForm" class="form-horizontal"')}


  <div class="col-md-offset-6 col-md-3">
    {$attr['name'] = '_submit'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '↓ 請求書情報 CSVダウンロード' , 'class="btn btn-warning btn-xs"')}
  </div>
</div>

{form_close()}

<ul class="pagination pagination-md">
  {$set_pagination}
</ul>

</div>




<script type="text/javascript">
$(function() {
  $('#invoice_all').on('click', function() {
    $('.invoice').prop('checked', this.checked);
  });

  $('.invoice').on('click', function() {
    if ($('#invoices :checked').length == $('#invoices :input').length){
      $('#invoice_all').prop('checked', 'checked');
    }else{
      $('#invoice_all').prop('checked', false);
    }
  });
});
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
