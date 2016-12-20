{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　手動データ　作成</p></H3>

{form_open('data_manual/receivable_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【債権データ作成】</label>
  </div>
  <div class="form-group">
    <label for="sales_date" class="col-xs-2 col-md-2 col-sm-offset-3 control-label">売上日 範囲指定<font color=red> *</font></label>
    <div class="col-md-5">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-4 col-md-2 col-sm-offset-4">
      {form_input('sales_date01' , set_value('sales_date01', '') , 'id="mydate2" class="form-control"')}
      {if form_error('sales_date01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sales_date01')}</font></label>{/if}
    </div>
    <div class="col-xs-1 col-md-1 text-center">～</div>
    <div class="col-xs-4 col-md-2">
      {form_input('sales_date02' , set_value('sales_date02', '') , 'id="mydate3" class="form-control"')}
      {if form_error('sales_date02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sales_date02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-1 col-sm-offset-4">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal02">作　成</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">債権データ作成</h4>
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
$('#mydate2').datepicker({
  format: "yyyy-mm-dd",
  daysOfWeekHighlighted: "0",
  todayBtn: "linked",
  autoclose: true,
  orientation: "bottom auto",
  clearBtn: true
});
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


{form_open('data_manual/sales_cal/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【指定日売上データ作成】</label>
  </div>
  <div class="form-group">
    <label for="sales_date" class="col-xs-2 col-md-2 col-sm-offset-3 control-label">売上日付 指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('sales_date' , set_value('sales_date', '') , 'id="mydate1" class="form-control"')}
      {if form_error('sales_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sales_date')}</font></label>{/if}
    </div>
    <div class="col-md-5">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-8 col-md-8 col-sm-offset-4">
      <p class="text-danger">※作成対象の「売上日」を指定してください。基本前日以前の日付を入力してください。</p>
      <p class="text-danger">※この処理は夜間バッチにて売上データが作成されなっかた場合に実行してください。</p>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-1 col-sm-offset-4">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal01">作　成</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">手動 売上データ作成</h4>
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
