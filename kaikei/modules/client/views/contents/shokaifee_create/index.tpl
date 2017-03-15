{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　紹介料　一括 or 個別 計算</p></H3>

{$mess}

{form_open('shokaifee_create/fix_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="issue_yymm" class="col-sm-3 control-label">【 紹介料計算　 】</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('issue_yymm', $options_issue_yymm, set_value('issue_yymm', ''))}
      {if form_error('issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('issue_yymm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="skf_issue_date01" class="col-xs-3 col-md-3 control-label">発行日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('skf_issue_date01' , set_value('skf_issue_date01', '') , 'id="mydate1" class="form-control"')}
      {if form_error('skf_issue_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_issue_date01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
    <div class="col-sm-1">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal10">一　括</button>
    </div>
  </div>
  <div class="form-group">
    <label for="skd_cm_seq01" class="col-xs-3 col-md-3 control-label">支払先情報番号</label>
    <div class="col-xs-4 col-md-2">
      {form_input('skd_cm_seq01' , set_value('skd_cm_seq01', '') , 'class="form-control"')}
      {if form_error('skd_cm_seq01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skd_cm_seq01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※該当顧客のみ支払計算を作成します。<br>※「個別」ボタンを押下してください。</small></p>
    </div>
    <div class="col-sm-1">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal11">個　別</button>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-9 col-md-9 col-sm-offset-3">
      <p class="text-danger">※毎月データを作成した後に、支払情報や顧客情報を変更しても反映されませんので注意してください。</p>
      <p class="text-danger">※複数回使用する場合は、既存の支払データは上書きされるので注意してください。</p>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal10" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">紹介料計算（一括）</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save_all' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <div class="modal fade" id="myModal11" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">紹介料計算（個別）</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save_oly' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<script type="text/javascript">
$('#mydate1').datepicker({
  format: "yyyy-mm-dd",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
</script>

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
