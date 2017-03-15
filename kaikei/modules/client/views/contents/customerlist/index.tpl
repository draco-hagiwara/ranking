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

<h4>【顧客情報　検索】</h4>
{form_open('/customerlist/search/' , 'name="searchForm" class="form-horizontal"')}
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">会 社 名</td>
        <td class="col-md-4">
          {form_input('cm_company' , set_value('cm_company', {$seach_company}) , 'class="form-control" placeholder="会社名を入力してください。"')}
          {if form_error('cm_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_company')}</font></label>{/if}
        </td>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('cm_status', $options_cm_status, set_value('cm_status', {$seach_status}))}
        </td>
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
      {form_button($attr , '検　　索' , 'class="btn btn-default"')}
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
        <th>ID</th>
        <th>status</th>
        <th>会社名<br>代表電話番号</th>
        <th>担当者<br>担当電話番号</th>
        <th>メールアドレス</th>
        <th>請求書<br>作成情報</th>
        <th></th>
      </tr>
    </thead>


    {foreach from=$list item=cm}
      <tbody>
        <tr>
          <td>
            {$cm.cm_seq}
          </td>
          <td>
            {if $cm.cm_status == "0"}<font color="#ffffff" style="background-color:royalblue">[ 有効 ]</font>
            {elseif $cm.cm_status == "1"}<font color="#ffffff" style="background-color:gray">[ 停止 ]</font>
            {elseif $cm.cm_status == "2"}<font color="#ffffff" style="background-color:gray">[ 解約 ]</font>
            {else}エラー
            {/if}
          </td>
          <td>
            {if $cm.cm_agency_flg==1}[代]{/if}{if $cm.cm_agency_seq!=0}[{$cm.cm_agency_seq}⇒]{/if}{$cm.cm_company|escape}
            <br>{$cm.cm_tel01|escape}
          </td>
          <td>
            {$cm.cm_person01|escape} {$cm.cm_person02|escape}
            <br>{$cm.cm_tel02|escape}
          </td>
          <td>
            {$cm.cm_mail}
            <br>{$cm.cm_mailsub}
          </td>
          <td>
            {if $cm.cm_invo_timing==0}[固定]{elseif $cm.cm_invo_timing==1}[成功]{elseif $cm.cm_invo_timing==2}[代理]{/if}
            <br>
            {if $cm.cm_collect==1}月末締め当月末
            {elseif $cm.cm_collect==2}月末締め翌月末
            {elseif $cm.cm_collect==3}月末締め翌々月10日
            {elseif $cm.cm_collect==4}月末締め翌々月15日
            {elseif $cm.cm_collect==5}月末締め翌々月25日
            {elseif $cm.cm_collect==6}月末締め翌々月末
            {/if}
          </td>
          <td class="text-right">
            {if $cm.cm_status == "0"}
              <button type="button" class="btn btn-warning btn-xs" onclick="fmSubmit('detailForm', '/client/projectlist/add/', 'POST', '{$cm.cm_seq}', 'chg_seq');">受注登録</button>
              {*<button type="button" class="btn btn-warning btn-xs" onclick="fmSubmit('detailForm', '/client/invo_create/invoice_cm/', 'POST', '{$cm.cm_seq}', 'chg_seq');">請求書作成</button>*}
            {else}
              <button type="button" class="btn btn-default btn-xs" >受注登録</button>
              {*<button type="button" class="btn btn-warning btn-xs" onclick="fmSubmit('detailForm', '/client/invo_create/invoice_cm/', 'POST', '{$cm.cm_seq}', 'chg_seq');">請求書作成</button>*}
            {/if}

            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/customerlist/detail/', 'POST', '{$cm.cm_seq}', 'chg_seq');">編　集</button><br>
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/customerlist/cp/', 'POST', '{$cm.cm_seq}', 'chg_seq');">複　写</button>
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
