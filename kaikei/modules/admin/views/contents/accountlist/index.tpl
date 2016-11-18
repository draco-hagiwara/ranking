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
  e1.setAttribute('name', 'chg_uniq');
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

<h4>【アカウント検索】</h4>
{form_open('/accountlist/search/' , 'name="searchForm" class="form-horizontal"')}

  <table class="table table-hover table-bordered">
    <tbody>

      <tr>
        <td class="col-sm-2">氏　　名</td>
        <td class="col-sm-4">
          {form_input('ac_name' , set_value('ac_name', '') , 'class="form-control" placeholder="氏名を入力してください。"')}
          {if form_error('ac_name')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_name')}</font></label>{/if}
        </td>
        <td class="col-sm-2">会社名</td>
        <td class="col-sm-4">
          {form_input('cl_company' , set_value('cl_company', '') , 'class="form-control" placeholder="会社名を入力してください。"')}
          {if form_error('cl_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_company')}</font></label>{/if}
        </td>
      </tr>
    </tbody>
  </table>

  <div class="row">
    <div class="col-sm-5 col-sm-offset-5">
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

{form_open('/accountlist/detail/' , 'name="detailForm" class="form-horizontal"')}

  <div class="form-horizontal col-sm-10 col-sm-offset-1">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>状態</th>
          <th>Type</th>
          <th>名前</th>
          <th>会社名</th>
          <th></th>
        </tr>
      </thead>

      {foreach from=$list item=ac}
      <tbody>
        <tr>
          <td>
            {$ac.ac_seq}
          </td>
          <td>
            {if $ac.ac_status == "0"}<font color="#ffffff" style="background-color:royalblue">[ 有　効 ]</font>
            {elseif $ac.ac_status == "1"}<font color="#ffffff" style="background-color:gray">[ 無　効 ]</font>
            {elseif $ac.ac_status == "9"}<font color="#ffffff" style="background-color:gray">[ 削　除 ]</font>
            {else}}エラー
            {/if}
          </td>
          <td>
            {if $ac.ac_type == "0"}<font color="#ffffff" style="background-color:limegreen">[ 一般ユーザ ]</font>
            {elseif $ac.ac_type == "1"}<font color="#ffffff" style="background-color:deeppink">[ 管　理　者 ]</font>
            {else}}エラー
            {/if}
          </td>
          <td>
            {$ac.ac_name01|escape}　{$ac.ac_name02|escape}
          </td>
          <td>
            {$ac.cl_company}
          </td>
          <td>
            {if $ac.ac_type != 2 || $ac.ac_seq == $smarty.session.a_memSeq || $smarty.session.a_memSeq == 1}
              <button type="submit" class="btn btn-success btn-xs" name="ac_uniq" value="{$ac.ac_seq}">編集</button>
            {/if}
          </td>
        </tr>
      </tbody>
      {foreachelse}
        検索結果はありませんでした。
      {/foreach}

    </table>
  </div>

{form_close()}


<div class="row">
  <div class="col-sm-2">
    <ul class="pagination pagination-sm">
      {$set_pagination}
    </ul>
  </div>
</div>

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
