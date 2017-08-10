{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

{form_open_multipart('/data_csv/kwlist_csvup/' , 'name="dataForm" class="form-horizontal"')}

  <div class="form-group form-group-lg">
    <label for="cl_detail" class="col-xs-7 col-md-7 control-label">【キーワード情報CSV：アップロード】　　　　　　　　　　　　　</label>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-xs-10 col-md-10">
      {form_upload('kw_data', '')}
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <br>・一回にアップロードできる件数は100件です。
      <br>・ファイルフォーマットは「順位データ一覧」よりダウンロードされるファイルに準じます。
      <br>・一行目は項目欄（必須）となります。
      <br>・一列目の「seq」にID番号が設定されている場合は既存情報の更新。設定されていない場合は追加となります。
      <br>・「ドメイン」,「ルートドメイン」欄は、「対象URL」から自動作成されます。
      <br>・
      <button type="button" class="btn btn-default  btn-sm" data-container="body" data-toggle="popover" data-placement="right"
        data-content="<b>【ステータス】</b><br>&emsp;1：有効，0：無効<br>
                      <b>【URL一致方式】</b><br>&emsp;0：完全一致，1：前方一致，2：ドメイン一致，3：ルートドメイン一致<br>
                      <b>【検索エンジン】</b><br>&emsp;0：Google，1：Yahoo!<br>
                      <b>【デバイス】</b><br>&emsp;0：ＰＣ版，1：モバイル版<br>
                      <b>【最大取得順位】</b><br>&emsp;0：100件，1：200件，2：300件<br>
                      <b>【データ取得回数】</b><br>&emsp;0：１回，1：２回，2：３回<br>
                      <b>【設定タグ】</b><br>&emsp;複数指定可能。'[]'で括ってください。<br>
                     ">
        項目コードの説明
      </button>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      {$up_mess02}
    </div>
  </div>

  <br><br>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal02">↑&emsp;アップロード</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">キーワード情報のCSVアップロード</h4>
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

<hr>

{form_open_multipart('/data_csv/project_csvup/' , 'name="dataForm" class="form-horizontal"')}

  <div class="form-group form-group-lg">
    <label for="cl_detail" class="col-xs-7 col-md-7 control-label">【キーワード（受注）情報CSV：アップロード（ラベンダー専用）】</label>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-xs-10 col-md-10">
      {form_upload('kw_data', '')}
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <br>・この処理はラベンダーマーケティングの受注ファイル専用です。
      <br>・一行目は項目欄となり、読み飛ばされます。
      <br>・
      <button type="button" class="btn btn-default  btn-sm" data-container="body" data-toggle="popover" data-placement="right"
        data-content="<b>【ステータス】</b><br>&emsp;1：有効，0：無効<br>
                      <b>【URL一致方式】</b><br>&emsp;0：完全一致，1：前方一致，2：ドメイン一致，3：ルートドメイン一致<br>
                      <b>【検索エンジン】</b><br>&emsp;0：Google，1：Yahoo!<br>
                      <b>【デバイス】</b><br>&emsp;0：ＰＣ版，1：モバイル版<br>
                      <b>【最大取得順位】</b><br>&emsp;0：100件，1：200件，2：300件<br>
                      <b>【データ取得回数】</b><br>&emsp;0：１回，1：２回，2：３回<br>
                     ">
        項目コードの説明
      </button>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      {$up_mess}
    </div>
  </div>

  <br><br>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal01">↑&emsp;アップロード</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">キーワード（受注）情報のCSVアップロード</h4>
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

{* POP-Over *}
<script src="{base_url()}../../js/my/popover.js"></script>

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
