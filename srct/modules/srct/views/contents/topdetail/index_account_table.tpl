{form_open('/topdetail/account_detail/' , 'name="detailForm" class="form-horizontal"')}

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
      {if ($smarty.session.c_memType==0)||($smarty.session.c_memType==9)||($smarty.session.c_memSeq==$ac.ac_seq)}
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
            {if $ac.ac_type == "0"}<span class="label label-danger">管&emsp;理&emsp;者</span>
            {elseif $ac.ac_type == "1"}<span class="label label-warning">利&emsp;用&emsp;者</span>
            {elseif $ac.ac_type == "2"}<span class="label label-info">閲&emsp;覧&emsp;者</span>
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
            {if ($smarty.session.c_memType==0)||($smarty.session.c_memSeq==$ac.ac_seq)}
              <button type="button" id="ac_update_btn{$ac.ac_seq}" class="btn btn-success btn-xs" data-toggle="modal">編&emsp;集</button>
            {/if}
          </td>
        </tr>
      </tbody>

      {/if}

      {* モーダル（編集/削除/レポート）処理 *}
      {include file="../../../../../public/js/my/account_modal.php"}

    {foreachelse}
      アカウント情報はありませんでした。
    {/foreach}

    </table>
  </div>

{form_close()}
