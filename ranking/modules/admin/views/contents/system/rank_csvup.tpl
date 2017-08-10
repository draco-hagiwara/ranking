{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}


{form_open_multipart('/system/criteria_csvup/' , 'name="dataForm" class="form-horizontal"')}

  <div class="form-group form-group-lg">
    <label for="cl_detail" class="col-xs-7 col-md-7 control-label">【Location Criteria情報CSV：アップロード】　　　　　　　　　　</label>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-xs-10 col-md-10">
      {form_upload('criteria_data', '')}
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <br>・基準ファイルのダウンロード先 ⇒ <a href="https://developers.google.com/adwords/api/docs/appendix/geotargeting" target="_blank">Geotargets</a>
      <br>・一行目は項目欄となり、読み飛ばされます。
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      {$up_mess03}
    </div>
  </div>

  <br><br>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal03">↑&emsp;アップロード</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal03" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Criteria情報のCSVアップロード</h4>
        </div>
        <div class="modal-body">
          <p>アップロードしますか。&hellip;</p>
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
