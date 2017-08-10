{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<script src="{base_url()}../../js/my/fmsubmit.js"></script>

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
          <th>状態</th>
          <th>Type</th>
          <th>名前</th>
          <th>ログインID</th>
          <th>作成日時</th>
          <th>最終ログイン日時</th>
          <th></th>
        </tr>
      </thead>

    {foreach from=$list item=ac}
      {if ($smarty.session.c_memType==1)||($smarty.session.c_memSeq==$ac.ac_seq)}
      <tbody>
        <tr>
          <td>
            {if $ac.ac_status == "0"}<span class="label label-primary">有&emsp;効</span>
            {elseif $ac.ac_status == "1"}<span class="label label-default">無&emsp;効</span>
            {elseif $ac.ac_status == "9"}<span class="label label-default">削&emsp;除</span>
            {else}}エラー
            {/if}
          </td>
          <td>
            {if $ac.ac_type == "0"}<span class="label label-success">一般ユーザ</span>
            {elseif $ac.ac_type == "1"}<span class="label label-danger">管&emsp;理&emsp;者</span>
            {else}}エラー
            {/if}
          </td>
          <td>
            {$ac.ac_name01|escape}　{$ac.ac_name02|escape}
          </td>
          <td>
            {$ac.ac_id}
          </td>
          <td>
            {$ac.ac_create_date}
          </td>
          <td>
            {$ac.ac_lastlogin}
          </td>
          <td>
            {if $ac.ac_type != 2 || $ac.ac_seq == $smarty.session.a_memSeq || $smarty.session.a_memSeq == 1}
              <button type="submit" class="btn btn-success btn-xs" name="ac_uniq" value="{$ac.ac_seq}">編&emsp;集</button>
            {/if}
          </td>
        </tr>
      </tbody>
      {/if}
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
