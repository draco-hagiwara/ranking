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

<h5>【 受注（案件）情報　検索 】</h5>
{form_open('/projectlist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">受注案件ID</td>
        <td class="col-md-2 input-group-sm">
          {form_input('pj_seq' , set_value('pj_seq', {$seach_seq}) , 'class="form-control"')}
          {if form_error('pj_seq')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_seq')}</font></label>{/if}
        </td>
        <td class="col-md-1">顧客CD</td>
        <td class="col-md-2 input-group-sm">
          {form_input('pj_cm_seq' , set_value('pj_cm_seq', {$seach_cm_seq}) , 'class="form-control"')}
          {if form_error('pj_cm_seq')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_cm_seq')}</font></label>{/if}
        </td>
        <td class="col-md-1">会 社 名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('pj_cm_company' , set_value('pj_cm_company', {$seach_cm_company}) , 'class="form-control"')}
          {if form_error('pj_cm_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_cm_company')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('pj_status', $options_pj_status, set_value('pj_status', {$seach_status}))}
        </td>
        <td class="col-md-1">請求書発行</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('pj_invoice_status', $options_pj_invoice_status, set_value('pj_invoice_status', {$seach_invoice_status}))}
        </td>
        <td class="col-md-1">課金方式</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('pj_accounting', $options_pj_accounting, set_value('pj_accounting', {$seach_accounting}))}
        </td>
        <td class="col-md-1">担当営業</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('pj_salesman', $options_pj_salesman, set_value('pj_salesman', {$seach_salesman}))}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">ID並び替え</td>
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

{form_open('/projectlist/detail/' , 'name="detailForm" class="form-horizontal"')}
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>status</th>
        <th>請求書</th>
        <th>課 金</th>
        <th>会 社 名 (下段：キーワード)</th>
        <th>固定金額</th>
        <th>契約期間</th>
        <th>担当営業</th>
        <th></th>
      </tr>
    </thead>


    {foreach from=$list item=pj}
      <tbody>
        <tr>
          <td>
            {$pj.pj_seq}
          </td>
          <td>
            {if $pj.pj_status == "0"}<span class="label label-primary">有 効</span>
            {elseif $pj.pj_status == "1"}<span class="label label-default">停 止</span>
            {elseif $pj.pj_status == "2"}<span class="label label-default">解 約</span>
            {else}エラー
            {/if}
          </td>
          <td>
            {if $pj.pj_invoice_status == "0"}<span class="label label-primary">発 行</span>
            {elseif $pj.pj_invoice_status == "1"}<span class="label label-default">停 止</span>
            {else}エラー
            {/if}
          </td>
          <td>
            {if $pj.pj_accounting == "0"}<font color="blue">【SEO】</font>
            {elseif $pj.pj_accounting == "1"}<font color="blue">【月額】</font>
            {elseif $pj.pj_accounting == "2"}<font color="deeppink">【成功】</font>
            {elseif $pj.pj_accounting == "3"}<font color="deeppink">【固+成】</font>
            {elseif $pj.pj_accounting == "10"}<font color="darkolivegreen">【アフィ】</font>
            {elseif $pj.pj_accounting == "11"}<font color="darkolivegreen">【広告】</font>
            {elseif $pj.pj_accounting == "12"}<font color="darkolivegreen">【その他】</font>
            {elseif $pj.pj_accounting == "7"}<font color="darkolivegreen">【保守】</font>
            {elseif $pj.pj_accounting == "8"}<font color="darkolivegreen">【前受】</font>
            {elseif $pj.pj_accounting == "9"}<font color="darkolivegreen">【赤伝】</font>
            {else}エラー
            {/if}
          </td>
          <td>
            [{$pj.pj_cm_seq}]{$pj.pj_cm_company}<br>> {$pj.pj_keyword}
          </td>
          <td>
            {$pj.pj_billing|number_format} 円
          </td>
          <td>
            {$pj.pj_start_date}<br> ～ {if $pj.pj_end_date<=$chk_end}<font color="red">{$pj.pj_end_date}</font>{else}{$pj.pj_end_date}{/if}
          </td>
          <td>
            {$options_pj_salesman[$pj.pj_salesman]}
            <br>{if $pj.pj_renew_chk==1}契約自延{/if}
          </td>
          <td class="text-right">
            {*<button type="button" class="btn btn-warning btn-xs" onclick="fmSubmit('detailForm', '/client/projectlist/detail/', 'POST', '{$pj.pj_seq}', 'chg_seq');">個別請求書作成</button>*}
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/projectlist/detail/', 'POST', '{$pj.pj_seq}', 'chg_seq');">編　集</button><br>
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/projectlist/cp/', 'POST', '{$pj.pj_seq}', 'chg_seq');">複　写</button>
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

  </table>

{form_close()}

{form_open('/data_csvup/projects_csvdown/' , 'name="csvForm" class="form-horizontal"')}

  <div class="col-md-12">
    {$attr['name'] = '_submit'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '↓ 受注案件情報のダウンロード' , 'class="btn btn-warning btn-xs"')}
  </div>

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
