{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　手動　順位データの取得</p></H3>

{form_open('rank_create/manual/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 順位データ取得 】</label>
    <div class="col-sm-2 input-lg">
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

<hr>
<div class="col-sm-10 col-sm-offset-2">
  {$mess}
</div>

<!-- </form> -->







{form_open('rank_create/manual_blob/' , 'name="createForm" class="form-horizontal h-adr"')}

  <div class="form-group form-group-lg">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">【 BLOBデータ取得 】</label>
    <div class="col-sm-2 input-lg">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal20">取得開始</button>
    </div>
  </div>

  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal20" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
