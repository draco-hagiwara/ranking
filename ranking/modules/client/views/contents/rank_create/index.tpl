{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　手動　順位データの取得</p></H3>

{form_open('rank_create/manual/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 順位データ取得 】</label>
    <div class="col-md-9">
      <p class="text-muted">・手動にて当日順位データを取得します。</p>
      <p class="text-muted">・当日順位データの取得中。または３回/日までしか取得はできません。</p>
    </div>
  </div>
  <div class="form-group form-group-lg">
    <div class="col-sm-offset-3 col-md-2 input-lg">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal10">取得開始</button>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal10" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">手動　順位データ取得</h4>
        </div>
        <div class="modal-body">
          <p>順位データ取得を開始しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='save_all' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<div class="col-sm-9 col-sm-offset-3">
  {$mess}
</div>

<!-- </form> -->

<br><br>
<hr>

{form_open('rank_create/irregular/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 イレギュラー処理 】</label>
    <div class="col-md-9">
      <p class="text-muted">・前日分の順位データをコピーし、当日の順位データとします。</p>
      <p class="text-muted">・以下に書換えを行う日付を指定してください。指定された前日の順位データを引き継ぎます。</p>
      <p class="text-muted">・処理前にバックアップを取ることを推奨します。</p>
    </div>
  </div>
  <div class="form-group">

    <div class="col-sm-offset-3 col-md-9">
      <label for="rk_getdate" class="control-label">書換日を指定<font color=red> *</font></label>
    </div>
    <div class="col-sm-offset-3 col-md-2">
      {form_input('rk_getdate' , set_value('rk_getdate', '') , 'id="mydate1" class="form-control"')}
    </div>
    <div class="col-md-4">
      <p><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
    <div class="col-sm-offset-3 col-md-9">
      {if form_error('rk_getdate')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rk_getdate')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group form-group-lg">
    <div class="col-sm-offset-3 col-md-2 input-lg">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal20">全データ書換</button>
    </div>
    <div class="col-sm-offset-1 col-md-2 input-lg">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal21">不足データのみ書換</button>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal20" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">手動　全順位データ書換</h4>
        </div>
        <div class="modal-body">
          <p>順位データ書換を開始しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='chg_all' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal21" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">手動　不足分の順位データ書換</h4>
        </div>
        <div class="modal-body">
          <p>順位データ取得を書換しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='chg_part' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<div class="col-sm-9 col-sm-offset-3">
  {$mess01}
</div>

<!-- </form> -->


<script src="{base_url()}../../js/my/rank_create_date.js"></script>


<hr>

<br><br>
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
