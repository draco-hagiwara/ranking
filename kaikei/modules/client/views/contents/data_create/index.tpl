{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　請求書データ　一括作成</p></H3>
<hr>

{form_open('data_create/fix_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">固定請求データ作成</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_fix, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
    <div class="col-sm-1 col-sm-offset-2">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal01">作　成</button>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_issue_date01" class="col-xs-3 col-md-3 control-label">発行日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_issue_date01' , set_value('iv_issue_date01', '') , 'id="mydate1" class="form-control"')}
      {if form_error('iv_issue_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_date01')}</font></label>{/if}
    </div>
    <div class="col-md-5">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-9 col-md-9 col-sm-offset-3">
      <p class="text-danger">※毎月データを作成した後に、顧客情報や案件情報を変更しても反映されませんので注意してください。</p>
      <p class="text-danger">※基本毎月一度の使用とします。</p>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">固定請求データ作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
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

{form_open('data_create/result_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">成果請求データ作成</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_res, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
    {*<div class="col-sm-1 col-sm-offset-2">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal02">作　成</button>
    </div>*}
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">成果請求データ作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->




{form_close()}


<hr>

{form_open('data_create/mix_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">固 + 成請求データ作成</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_res, set_value('iv_issue_yymm', ''))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
    {*<div class="col-sm-1 col-sm-offset-2">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal03">作　成</button>
    </div>*}
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal03" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">固 + 成請求データ作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
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
