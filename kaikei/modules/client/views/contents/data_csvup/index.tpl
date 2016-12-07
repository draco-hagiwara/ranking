{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　CSVデータ　アップロード</p></H3>

{form_open_multipart('/data_csvup/customer_csvup/' , 'name="datachkForm" class="form-horizontal"')}

  <div class="row">
    <label for="cl_detail" class="col-xs-4 col-md-4 control-label">【顧客マスタCSV：アップロード】</label>
    <div class="col-xs-8 col-md-8">
      {form_upload('cm_data', '')}
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-4 col-sm-8">
    ・クライアント[会社名]/[会社名カナ]/[]/[]　のみの更新としています。
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-4 col-sm-8">
      {$up_mess01}
    </div>
  </div>

  <br><br>
  <div class="row">
    <div class="col-sm-offset-4 col-sm-8">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal01">アップロード</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">顧客マスタ作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<!-- </form> -->

<hr>

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
