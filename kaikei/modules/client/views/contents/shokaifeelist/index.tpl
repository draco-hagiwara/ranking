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

<h5>【 支払（紹介料）情報　検索 】</h5>
{form_open('/shokaifeelist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">支払通知書</td>
        <td class="col-md-2 input-group-sm">
          {form_input('skf_pay_no' , set_value('skf_pay_no', {$seach_skf_pay_no}) , 'class="form-control"')}
          {if form_error('skf_pay_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_pay_no')}</font></label>{/if}
        </td>
        <td class="col-md-1">支払先会社</td>
        <td class="col-md-2 input-group-sm">
          {form_input('skf_sk_company' , set_value('skf_sk_company', {$seach_skf_sk_company}) , 'class="form-control"')}
          {if form_error('skf_sk_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_sk_company')}</font></label>{/if}
        </td>
        <td class="col-md-1">支払月<font color=red> *</font></td>
        <td class="col-md-2 input-group-sm">
          {form_input('skf_pay_date01' , set_value('skf_pay_date01', {$seach_skf_pay_date01}) , 'class="form-control" placeholder="yyyy-mm"')}
          {if form_error('skf_pay_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_pay_date01')}</font></label>{/if}
        </td>
        <td class="col-md-1 text-center">　～　</td>
        <td class="col-md-2 input-group-sm">
          {form_input('skf_pay_date02' , set_value('skf_pay_date02', {$seach_skf_pay_date02}) , 'class="form-control" placeholder="yyyy-mm"')}
          {if form_error('skf_pay_date02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_pay_date02')}</font></label>{/if}
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

{form_open('/customerlist/detail/' , 'name="detailForm" class="form-horizontal"')}

  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th></th>
        <th>支払月</th>
        <th>支払通知書NO</th>
        <th>支払先会社名</th>
        <th>振込金額</th>
        <th>発行日</th>
        <th>振込日</th>
        <th></th>
      </tr>
    </thead>

    {foreach from=$list item=skf name="seq"}
      <tbody>
        <tr>
          <td>
            {if $skf.skf_status==0}<font color="#ffffff" style="background-color:royalblue">[未発行]</font>
            {elseif $skf.skf_status==1}<font color="#ffffff" style="background-color:dimgray">[発行済]</font>
            {elseif $skf.skf_status==9}<font color="#ffffff" style="background-color:dimgray">[削　除]</font>
            {else}[ERROR]{/if}
          </td>
          <td>
            {$skf.skf_pay_yymm|substr:0:4}-{$skf.skf_pay_yymm|substr:4:2}
          </td>
          <td>
            {$skf.skf_pay_no}
          </td>
          <td>
            {$skf.skf_sk_company}
          </td>
          <td>
            {($skf.skf_pay_total+$skf.skf_pay_tax)|number_format} 円
          </td>
          <td>
            {$skf.skf_issue_date}
          </td>
          <td>
            {$skf.skf_pay_date}
          </td>
          <td class="text-right">
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/shokaifeelist/detail/', 'POST', '{$skf.skf_seq}', 'chg_seq');">編　集</button><br>
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>
{form_close()}


{form_open('/shokaifeelist/csvdown/' , 'name="detailForm" class="form-horizontal"')}

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
