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

<H3><p class="bg-success">　　アカウント一覧</p></H3>

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
          <th>メールアドレス</th>
          <th>最終ログイン日時</th>
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
            {$ac.ac_mail}
          </td>
          <td>
            {$ac.ac_lastlogin}
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
