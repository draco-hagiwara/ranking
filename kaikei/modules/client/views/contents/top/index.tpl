{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<div class="row">
  <label for="cm_cnt_enable" class="col-xs-3 col-md-3 control-label">顧客登録ステータス（有効）</label>
  <div class="col-xs-1 col-md-1">
    {$cm_cnt_enable} 社
  </div>
</div>
<div class="row">
  <label for="cm_cnt_pause" class="col-xs-3 col-md-3 control-label">顧客登録ステータス（一時停止）</label>
  <div class="col-xs-1 col-md-1">
    {$cm_cnt_pause} 社
  </div>
</div>
<div class="row">
  <label for="cm_cnt_disable" class="col-xs-3 col-md-3 control-label">顧客登録ステータス（キャンセル）</label>
  <div class="col-xs-1 col-md-1">
    {$cm_cnt_disable} 社
  </div>
</div>

<hr>

<div class="row">
  <label for="pj_cnt_enable" class="col-xs-3 col-md-3 control-label">受注案件ステータス（有効）</label>
  <div class="col-xs-1 col-md-1">
    {$pj_cnt_enable} 社
  </div>
</div>
<div class="row">
  <label for="pj_cnt_pause" class="col-xs-3 col-md-3 control-label">受注案件ステータス（一時停止）</label>
  <div class="col-xs-1 col-md-1">
    {$pj_cnt_pause} 社
  </div>
</div>
<div class="row">
  <label for="pj_cnt_disable" class="col-xs-3 col-md-3 control-label">受注案件ステータス（キャンセル）</label>
  <div class="col-xs-1 col-md-1">
    {$pj_cnt_disable} 社
  </div>
</div>

<hr>

<div class="row">
  <label for="iv_date_fix" class="col-xs-3 col-md-3 control-label">請求書　【{$iv_date_fix}】</label>
</div>
<div class="row">
  <label for="iv_cnt_enable" class="col-xs-3 col-md-3 control-label">請求書ステータス（未発行）</label>
  <div class="col-xs-1 col-md-1">
    {$iv_cnt_enable} 社
  </div>
</div>
<div class="row">
  <label for="iv_cnt_pause" class="col-xs-3 col-md-3 control-label">請求書ステータス（発行済）</label>
  <div class="col-xs-1 col-md-1">
    {$iv_cnt_pause} 社
  </div>
</div>
<div class="row">
  <label for="iv_cnt_disable" class="col-xs-3 col-md-3 control-label">請求書ステータス（キャンセル）</label>
  <div class="col-xs-1 col-md-1">
    {$iv_cnt_disable} 社
  </div>
</div>






<br><br><br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
