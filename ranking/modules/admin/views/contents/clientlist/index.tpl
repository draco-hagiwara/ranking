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
  e1.setAttribute('name', 'chg_clseq');
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

<h4>【クライアント検索】</h4>
{form_open('/clientlist/search/' , 'name="searchForm" class="form-horizontal"')}
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">会 社 名</td>
        <td class="col-md-4">
          {form_input('cl_company' , set_value('cl_company', {$seach_company}) , 'class="form-control" placeholder="会社名を入力してください。"')}
          {if form_error('cl_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_company')}</font></label>{/if}
        </td>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-lg">
          {form_dropdown('cl_status', $options_cl_status, set_value('cl_status', {$seach_status}))}
        </td>
        <td class="col-md-1">ID並び替え</td>
        <td class="col-md-2  btn-lg">
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

{form_open('/clientlist/detail/' , 'name="detailForm" class="form-horizontal"')}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>ステータス</th>
                <th>会社名<br>代表電話番号</th>
                <th>担当者<br>担当電話番号</th>
                <th>メールアドレス</th>
                <th class="col-md-1"></th>
            </tr>
        </thead>


        {foreach from=$list item=cl}
        <tbody>
            <tr>
                <td>
                    {$cl.cl_seq}
                </td>
                <td>
                    {if $cl.cl_status == "0"}<font color="#ffffff" style="background-color:royalblue">[ 運 用 中 ]</font>
                    {elseif $cl.cl_status == "1"}<font color="#ffffff" style="background-color:gray">[ 一時停止 ]</font>
                    {elseif $cl.cl_status == "8"}<font color="#ffffff" style="background-color:gray">[ 解　約 ]</font>
                    {else}エラー
                    {/if}
                </td>
                <td>
                    {$cl.cl_company|escape}<br>{$cl.cl_tel01|escape}
                </td>
                <td>
                    {$cl.cl_person01|escape} {$cl.cl_person02|escape}<br>{$cl.cl_tel02|escape}
                </td>
                <td>
                    {$cl.cl_mail}<br>{$cl.cl_mailsub}
                </td>
                <td>
                    <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/admin/clientlist/detail/', 'POST', '{$cl.cl_seq}', 'chg_clseq');">編　集</button>
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
