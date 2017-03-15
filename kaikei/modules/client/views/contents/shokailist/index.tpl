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

<h4>【支払先情報　検索】</h4>
{form_open('/shokailist/search/' , 'name="searchForm" class="form-horizontal"')}
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">会 社 名</td>
        <td class="col-md-4">
          {form_input('sk_company' , set_value('sk_company', {$seach_company}) , 'class="form-control" placeholder="会社名を入力してください。"')}
          {if form_error('sk_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_company')}</font></label>{/if}
        </td>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('sk_status', $options_sk_status, set_value('sk_status', {$seach_status}))}
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

{form_open('/shokailist/detail/' , 'name="detailForm" class="form-horizontal"')}
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>status</th>
        <th>会社名</th>
        <th>担当者</th>
        <th>担当電話番号</th>
        <th>支払サイクル</th>
        <th>担当営業</th>
        <th></th>
      </tr>
    </thead>


    {foreach from=$list item=sk}
      <tbody>
        <tr>
          <td>
            {$sk.sk_seq}
          </td>
          <td>
            {if $sk.sk_status == "0"}<font color="#ffffff" style="background-color:royalblue">[ 有効 ]</font>
            {elseif $sk.sk_status == "1"}<font color="#ffffff" style="background-color:gray">[ 停止 ]</font>
            {elseif $sk.sk_status == "2"}<font color="#ffffff" style="background-color:gray">[ 解約 ]</font>
            {else}エラー
            {/if}
          </td>
          <td>
            {$sk.sk_company|escape}
          </td>
          <td>
            {$sk.sk_person01|escape} {$sk.sk_person02|escape}
          </td>
          <td>
            {$sk.sk_tel02|escape}
          </td>
          <td>
            {$options_sk_payment[$sk.sk_payment]|escape}
          </td>
          <td>
            {$options_sk_salesman[$sk.sk_salesman]|escape}
          </td>
          <td class="text-right">
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/shokailist/detail/', 'POST', '{$sk.sk_seq}', 'chg_seq');">編　集</button>
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
