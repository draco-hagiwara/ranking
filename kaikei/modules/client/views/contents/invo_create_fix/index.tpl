{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　請求書データ　一括 or 個別 作成</p></H3>

{$mess}
<hr>

{form_open('invo_create_fix/fix_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 固定関連請求データ作成　 】</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_fix, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
    <div class="col-sm-offset-4 col-sm-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal11">個　別</button>
    </div>
    <div class="col-sm-offset-1 col-sm-1">
      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal10">一　括</button>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_issue_date01" class="col-xs-3 col-md-3 control-label">発行日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_issue_date01' , set_value('iv_issue_date01', '') , 'id="mydate1" class="form-control"')}
      {if form_error('iv_issue_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_date01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_cm_seq01" class="col-xs-3 col-md-3 control-label">顧客番号</label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_cm_seq01' , set_value('iv_cm_seq01', '') , 'class="form-control"')}
      {if form_error('iv_cm_seq01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_cm_seq01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※該当顧客のみ請求書データを作成します。<br>※「個別」ボタンを押下してください。</small></p>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-9 col-md-9 col-sm-offset-3">
      <p class="text-danger">※「一括」作成を複数回使用する場合は、既存の請求書データが上書きされるので注意してください。</p>
      <p class="text-danger">※データを作成した後に、顧客情報や案件情報を変更しても反映されませんので注意してください。</p>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal11" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">固定関連請求データ作成（個別）</h4>
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
  <div class="modal fade" id="myModal10" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">固定関連請求データ作成（一括）</h4>
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

<hr>

{form_open('invo_create_result/result_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 成功関連請求データ作成　 】</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_res, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
    <div class="col-sm-offset-4 col-sm-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal21">個　別</button>
    </div>
    <div class="col-sm-offset-1 col-sm-1">
      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal20">一　括</button>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_issue_date01" class="col-xs-3 col-md-3 control-label">発行日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_issue_date01' , set_value('iv_issue_date01', '') , 'id="mydate2" class="form-control"')}
      {if form_error('iv_issue_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_date01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_cm_seq02" class="col-xs-3 col-md-3 control-label">顧客番号</label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_cm_seq02' , set_value('iv_cm_seq02', '') , 'class="form-control"')}
      {if form_error('iv_cm_seq02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_cm_seq02')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※該当顧客のみ請求書データを作成します。<br>※「個別」ボタンを押下してください。</small></p>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal21" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">成果関連請求データ作成（個別）</h4>
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
  <div class="modal fade" id="myModal20" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">成果関連請求データ作成（一括）</h4>
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

{form_close()}

<script type="text/javascript">
$('#mydate2').datepicker({
  format: "yyyy-mm-dd",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
</script>

<hr>

{form_open('invo_create_agency/agency_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 代理店関連請求データ作成 】</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_res, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
    <div class="col-sm-offset-4 col-sm-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal31">個　別</button>
    </div>
    <div class="col-sm-offset-1 col-sm-1">
      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal30">一　括</button>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_issue_date01" class="col-xs-3 col-md-3 control-label">発行日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_issue_date01' , set_value('iv_issue_date01', '') , 'id="mydate3" class="form-control"')}
      {if form_error('iv_issue_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_date01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_cm_seq03" class="col-xs-3 col-md-3 control-label">顧客番号</label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_cm_seq03' , set_value('iv_cm_seq03', '') , 'class="form-control"')}
      {if form_error('iv_cm_seq03')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_cm_seq03')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      <p class="redText"><small>※該当顧客のみ請求書データを作成します。<br>※「個別」ボタンを押下してください。</small></p>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal31" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">代理店関連請求データ作成（個別）</h4>
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
  <div class="modal fade" id="myModal30" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">代理店関連請求データ作成（一括）</h4>
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

{form_close()}

<script type="text/javascript">
$('#mydate3').datepicker({
  format: "yyyy-mm-dd",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
</script>

<!-- </form> -->

<hr>

{form_open('invo_create_fix/paydate_chg/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_pay_date" class="col-sm-3 control-label">【 振込期日の一括変更 】</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_fix, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-3 col-md-3 control-label">変更前→変更後 日付指定<font color=red> *</font></label>
    <div class="col-xs-4 col-sm-2">
      {form_input('iv_pay_date01' , set_value('iv_pay_date01', '') , 'id="mydate40" class="form-control" placeholder="yyyy-mm-dd"')}
      {if form_error('iv_pay_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_pay_date01')}</font></label>{/if}
    </div>
    <div class="col-xs-2 col-sm-1 text-center">⇒ ⇒ ⇒</div>
    <div class="col-xs-4 col-sm-2">
      {form_input('iv_pay_date02' , set_value('iv_pay_date02', '') , 'id="mydate41" class="form-control" placeholder="yyyy-mm-dd"')}
      {if form_error('iv_pay_date02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_pay_date02')}</font></label>{/if}
    </div>
    <div class="col-sm-1 col-sm-offset-1">
      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal40">変　更</button>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-9 col-md-9 col-sm-offset-3">
      <p class="text-danger">※ステータスが「未発行」の請求書が対象です。</p>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal40" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">振込期日の一括変更</h4>
        </div>
        <div class="modal-body">
          <p>変更しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save_all' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<script type="text/javascript">
$('#mydate40').datepicker({
  format: "yyyy-mm-dd",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
$('#mydate41').datepicker({
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
