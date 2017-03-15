{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
<script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>
{*<script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.ja.min.js"></script>*}

<body>

{* ヘッダー部分　END *}

<H3><p class="bg-success">　　案件情報　新規登録</p></H3>

{form_open('projectlist/cpchk/' , 'name="accountForm" class="form-horizontal"')}

  {$mess}
  <div class="form-group">
    <label for="pj_cm_company" class="col-sm-3 control-label">会社名<font color=red> *</font></label>
    <div class="col-xs-8 col-md-8">
      {$pj_cm_company}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_status" class="col-sm-3 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('pj_status', $options_pj_status, set_value('pj_status', $info_pj.pj_status))}
      {if form_error('pj_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_invoice_status" class="col-sm-3 control-label">請求書発行ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('pj_invoice_status', $options_pj_iv_type, set_value('pj_invoice_status', $info_pj.pj_invoice_status))}
      {if form_error('pj_invoice_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_invoice_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_orders_ymd" class="col-xs-6 col-md-3 control-label">受注年月日<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_orders_ymd' , set_value('pj_orders_ymd', $info_pj.pj_orders_ymd) , 'id="mydate3" class="form-control" placeholder="受注年月日"')}
      {if form_error('pj_orders_ymd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_orders_ymd')}</font></label>{/if}
    </div>
    <label for="pj_orders_start" class="col-xs-4 col-md-2 control-label">当初契約開始日</label>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_orders_start' , set_value('pj_orders_start', $info_pj.pj_orders_start) , 'id="mydate4" class="form-control" placeholder="契約開始年月日"')}
      {if form_error('pj_orders_start')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_orders_start')}</font></label>{/if}
    </div><br>
    <div class="col-md-5 col-md-offset-3">
      <p class="redText"><small>※入力フォーマット（ yyyy-dd-mm　または　yyyy/dd/mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <label for="pj_contract" class="col-xs-3 col-md-3 control-label">契約期間<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_start_date' , set_value('pj_start_date', $info_pj.pj_start_date) , 'id="mydate1" class="form-control" placeholder="開始日"')}
      {if form_error('pj_start_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_start_date')}</font></label>{/if}
    </div>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_end_date' , set_value('pj_end_date', $info_pj.pj_end_date) , 'id="mydate2" class="form-control" placeholder="終了日"')}
      {if form_error('pj_end_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_end_date')}</font></label>{/if}
    </div><br>
    <div class="col-md-5 col-md-offset-3">
      <p class="redText"><small>※入力フォーマット（ yyyy-dd-mm　または　yyyy/dd/mm ）</small></p>
      {if $err_date==TRUE}<span class="label label-danger">Error : </span><label><font color=red>「契約期間」欄で入力した日付が不整合です。</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="pj_renew" class="col-xs-3 col-md-3 control-label">契約自動延長有無</label>
    <div class="col-md-2">
      <label>{form_checkbox('pj_renew_chk','1',"{if $renew==1}1{else}0{/if}")}契約延長する</label>
    </div>
    <div class="col-xs-2 col-md-1">
      {form_input('pj_renew_mm' , set_value('pj_renew_mm', $info_pj.pj_renew_mm) , 'class="form-control"')}
    </div>
    <div class="col-xs-3 col-md-3">
      ヶ月自動で延長
      {if form_error('pj_renew_mm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_renew_mm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_accounting" class="col-xs-3 col-md-3 control-label">課金方式<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2 btn-lg">
      {form_dropdown('pj_accounting', $options_pj_accounting, set_value('pj_accounting', $info_pj.pj_accounting))}
      {if form_error('pj_accounting')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_accounting')}</font></label>{/if}
    </div>
  </div>
{*<div class="form-group">
    <label for="pj_accounting" class="col-xs-3 col-md-3 control-label">課金方式<font color=red> *</font></label>
    <div class="radio">
      <label class="col-xs-2 col-md-2 control-label">
        <input type="radio" name="pj_accounting" id="optionsRadios1" value="0" checked>固定
      </label>
      <label class="col-xs-2 col-md-2 control-label">
        <input type="radio" name="pj_accounting" id="optionsRadios2" value="1">成果報酬
      </label>
      <label class="col-xs-2 col-md-2 control-label">
        <input type="radio" name="pj_accounting" id="optionsRadios3" value="2">固定+成果
      </label>
    </div>
  </div>
*}
  <div class="form-group">
    <label for="pj_keyword" class="col-xs-3 col-md-3 control-label">検索キーワード<font color=red> *</font></label>
    <div class="col-xs-8 col-md-8">
      {form_input('pj_keyword' , set_value('pj_keyword', $info_pj.pj_keyword) , 'class="form-control" placeholder="検索キーワードを入力してください。max.100文字"')}
      {if form_error('pj_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_keyword')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_url" class="col-xs-3 col-md-3 control-label">対象URL<font color=red> *</font></label>
    <div class="col-xs-8 col-md-8">
      {form_input('pj_url' , set_value('pj_url', $info_pj.pj_url) , 'class="form-control" placeholder="対象URLを入力してください。max.100文字"')}
      {if form_error('pj_url')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_url')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_target" class="col-xs-3 col-md-3 control-label">順位取得対象<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
    </div>
  </div>
  <div class="form-group">
    <label for="pj_language" class="col-xs-3 col-md-3 control-label">対象言語<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
    </div>
  </div>
  <div class="form-group">
    <label for="pj_url_match" class="col-xs-3 col-md-3 control-label">URL一致方式<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
    </div>
  </div>
  <div class="form-group">
    <label for="pj_billing" class="col-xs-3 col-md-3 control-label">固定請求金額<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_billing' , set_value('pj_billing', $info_pj.pj_billing) , 'class="form-control"')}
      {if form_error('pj_billing')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_billing')}</font></label>{/if}
    </div>
    <div class="col-xs-1 col-md-1">
      <p class="redText">円</p>
    </div>
  </div>
  <div class="form-group">
    <label for="pjd_rank" class="col-xs-3 col-md-3 control-label">順位チェック調査対象</label>
    <div class="col-md-offset-3 col-md-8">■ 検索エンジン選択：<br />
      <label>{form_checkbox('chkengine[]','0',"{if $engine_g==1}1{else}0{/if}")} google.co.jp</label>
      <br />
      <label>{form_checkbox('chkengine[]','1',"{if $engine_y==1}1{else}0{/if}")} yahoo.co.jp</label>
    </div>

  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-8">■ 成果報酬選択時は以下を設定：</div>
  </div>
  <div class="form-group">
    <div class="col-xs-2 col-md-1 col-md-offset-3 text-right"></div>
    <div class="col-xs-2 col-md-1">開始順位</div>
    <div class="col-xs-2 col-md-1">終了順位</div>
    <div class="col-xs-4 col-md-2">報酬金額（円）</div>
  </div>
  {section name=counter loop=10}
    {$num=$smarty.section.counter.index|string_format:"%02d"}
    <div class="form-group">
      <div class="col-xs-2 col-md-1 col-md-offset-3 text-right">{$num+1}</div>
      <div class="col-xs-2 col-md-1">
        {form_input("pjd_rank_str01{$num}" , set_value("pjd_rank_str01{$num}", $pjd_rank_str01{$num}) , 'class="form-control"')}
        {if form_error("pjd_rank_str01{$num}")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("pjd_rank_str01{$num}")}</font></label>{/if}
      </div>
      <div class="col-xs-2 col-md-1">
        {form_input("pjd_rank_end01{$num}" , set_value("pjd_rank_end01{$num}", $pjd_rank_end01{$num}) , 'class="form-control"')}
        {if form_error("pjd_rank_end01{$num}")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("pjd_rank_end01{$num}")}</font></label>{/if}
      </div>
      <div class="col-xs-4 col-md-2 text-right">
        {form_input("pjd_billing01{$num}" , set_value("pjd_billing01{$num}", $pjd_billing01{$num}) , 'class="form-control"')}
        {if form_error("pjd_billing01{$num}")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("pjd_billing01{$num}")}</font></label>{/if}
      </div>
    </div>
  {/section}
  <div class="form-group">
    <label for="pj_salesman" class="col-md-3 control-label">担当営業<font color=red> *</font></label>
    <div class="col-md-2 btn-lg">
      {form_dropdown('pj_salesman', $options_pj_salesman, set_value('pj_salesman', $pj_salesman))}
      {if form_error('pj_salesman')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_salesman')}</font></label>{/if}
    </div>
  </div>


  <hr>

  <div class="form-group">
    <label for="pj_paycal" class="col-md-3 control-label">紹介料計算情報設定</label>
    <div class="col-md-2">
      固定金額：{form_input('pj_paycal_fix' , set_value('pj_paycal_fix', $info_pj.pj_paycal_fix) , 'class="form-control" placeholder="固定金額"')}
      {if form_error('pj_paycal_fix')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_paycal_fix')}</font></label>{/if}
    </div>
    <div class="col-md-2">
      料率：{form_input('pj_paycal_rate' , set_value('pj_paycal_rate', $info_pj.pj_paycal_rate) , 'class="form-control" placeholder="料率"')}
      {if form_error('pj_paycal_rate')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_paycal_rate')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      ⇒⇒　<u><font color=blue>固定金額</font>　+（<font color=blue> 料率</font>　×　売上高 ）</u>
    </div>
  </div>

  <hr>


  <div class="form-group">
    <label for="pj_memo" class="col-sm-3 control-label">備　　考</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="pj_memo" name="pj_memo" placeholder="max.1000文字">{$tmp_memo}</textarea>
      {if form_error('pj_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_memo')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_tag" class="col-xs-3 col-md-3 control-label">タグ設定</label>
    <div class="col-xs-8 col-md-8">
      {form_input('pj_tag' , set_value('pj_tag', '') , 'class="form-control" placeholder="タグを入力してください。max.100文字"')}
      {if form_error('pj_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_tag')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('pj_cm_seq', $pj_cm_seq)}
  {form_hidden('pj_cm_company', $pj_cm_company)}

  <br>
  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-3 col-sm-offset-3">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">登　　録</button>
    </div>
  </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">案件情報　登録</h4>
        </div>
        <div class="modal-body">
          <p>登録しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<!-- </form> -->

<script type="text/javascript">
$('#mydate1').datepicker({
  format: "yyyy-mm-dd",
  //language: "ja",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
$('#mydate2').datepicker({
  format: "yyyy-mm-dd",
  //language: "ja",
  startView: 1,
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
$('#mydate3').datepicker({
  format: "yyyy-mm-dd",
  //language: "ja",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
$('#mydate4').datepicker({
	  format: "yyyy-mm-dd",
	  //language: "ja",
	  daysOfWeekHighlighted: "0",
	  todayBtn: "linked",
	  autoclose: true,
	  orientation: "bottom auto",
	  clearBtn: true
	});
</script>

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
