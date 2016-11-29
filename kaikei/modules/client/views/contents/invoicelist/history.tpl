{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　請求書情報　履歴表示</p></H3>

  <div class="form-group">
  <ul class="pagination pagination-sm">
    検索結果： {$countall}件<br />
    {$set_pagination}
  </ul>
  </div>

  <div class="row">
    <label class="col-xs-2 col-md-2 control-label">作成日時</label>
    <div class="col-xs-10 col-md-10">{$list.iv_create_date}</div>
  </div>
  <div class="row">
    <label class="col-xs-2 col-md-2 control-label">ステータス選択</label>
    <div class="col-xs-9 col-md-9">
      {if $list.iv_status==0}未発行
      {elseif $list.iv_status==1}発行済
      {elseif $list.iv_status==9}キャンセル
      {else}エラー
      {/if}
    </div>
  </div>
  <div class="row">
    <label class="col-xs-2 col-md-2 control-label">請求書NO</label>
    <div class="col-xs-10 col-md-10">{$list.iv_slip_no}</div>
  </div>
  <div class="row">
    <label class="col-xs-2 col-md-2 control-label">発行日</label>
    <div class="col-xs-10 col-md-10">{$list.iv_issue_date}</div>
  </div>
  <div class="row">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">振込期日</label>
    <div class="col-xs-10 col-md-10">{$list.iv_pay_date}</div>
  </div>
  <div class="row">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">送付先住所</label>
    <div class="col-md-8">〒{$list.iv_zip01}-{$list.iv_zip02}</div>
    <div class="col-md-8">{$list.iv_pref} {$list.iv_addr01} {$list.iv_addr02} {$list.iv_buil}</div><br><br>
    <div class="col-md-8 col-md-offset-2">{$list.iv_company}</div>
    <div class="col-md-8 col-md-offset-2">{$list.iv_department}</div><br>
    <div class="col-md-8 col-md-offset-2">{$list.iv_person01} {$list.iv_person02}</div>
  </div>
  <div class="row">
    <label for="iv_bank_cd" class="col-xs-2 col-md-2 control-label">銀行情報</label>
    <div class="col-md-8">（{$list.iv_bank_cd}）{$list.iv_bank_nm}</div>
    <div class="col-md-8">（{$list.iv_branch_cd}）{$list.iv_branch_nm}</div>
    <div class="col-md-8 col-md-offset-2">（{$list.iv_account_no}）{$list.iv_account_nm}</div>
  </div>


<hr>

  <div class="row">
  <table class="table table-hover table-bordered">
    <tbody>
      <tr class="active">
        <td class="col-md-7 text-center">請　求　項　目</td>
        <td class="col-md-1 text-center">数 量</td>
        <td class="col-md-1 text-center">単 価（円）</td>
        <td class="col-md-1 text-center">金 額（円）</td>
      </tr>

      {foreach from=$list_d item=ivd}
      <tr>
        <td class="col-md-7 input-group-sm">
          対象キーワード：「{$ivd.ivd_item}」
        </td>
        <td class="col-md-1 input-group-sm text-center">
          {$ivd.ivd_qty|number_format}
        </td>
        <td class="col-md-1 input-group-sm text-right">
          {$ivd.ivd_price|number_format}
        </td>
        <td class="col-md-1 input-group-sm text-right">
          {$ivd.ivd_total|number_format}
        </td>
      </tr>
      {/foreach}

      <tr>
        <td colspan="3" class="input-group-sm text-right">小計</td>
        <td class="col-md-1 input-group-sm text-right">{$list.iv_subtotal|number_format}</td>
      </tr>
      <tr>
        <td colspan="3" class="input-group-sm text-right">消費税等</td>
        <td class="col-md-1 input-group-sm text-right">{$list.iv_tax|number_format}</td>
      </tr>
      <tr>
        <td colspan="3" class="input-group-sm text-right">合計</td>
        <td class="col-md-1 input-group-sm text-right">{$list.iv_total|number_format}</td>
      </tr>
    </tbody>
  </table>
  </div>

  <div class="row">
    <label for="iv_remark" class="col-sm-2 control-label">請求書：備考</label>
    <div class="col-md-10">{$list.iv_remark}</div>
  </div>
  <div class="row">
    <label for="iv_memo" class="col-sm-2 control-label">メ　　　　モ</label>
    <div class="col-md-10">{$list.iv_memo}</div>
  </div>


<!-- </form> -->

<br><br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
