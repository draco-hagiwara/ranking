{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

{form_open_multipart('/data_csvup/customer_csvup/' , 'name="dataForm" class="form-horizontal"')}

  <div class="form-group form-group-lg">
    <label for="cl_detail" class="col-xs-4 col-md-4 control-label">【顧客情報CSV：アップロード】</label>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-xs-10 col-md-10">
      {form_upload('cm_data', '')}
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <br>・一行目は項目欄となり、読み飛ばされます。
      <br>・一列目の「cm_seq」に顧客番号が設定されていない場合は追加、設定されている場合は既存情報の更新となります。
      <br>・
      <button type="button" class="btn btn-default  btn-sm" data-container="body" data-toggle="popover" data-placement="right"
        data-content="<b>【ステータス】</b><br>　0：有効，1：一時停止，2：解約<br>
                      <b>【代理店親フラグ】</b><br>　0：非代理店，1：代理店<br>
                      <b>【上場フラグ】</b><br>　0：非上場，1：東京，2：JASDAQ，3：大阪，4：名古屋，5：札幌，6：福岡，7：その他<br>
                      <b>【回収サイト】</b><br>　1：月末締め当月末，2：翌月末，3：翌々月10日，4：翌々月15日，5：翌々月25日，6：翌々月末<br>
                      <b>【請求書：有無フラグ別住所】</b><br>　0：住所なし，1：別住所<br>
                      <b>【請求書一括作成順序】</b><br>　0：作成する，1：作成しない<br>
                      <b>【担当営業】</b><br>　2：米元，3：大浦，4：工藤，5：辻，6：<br>
                      <b>【削除フラグ】</b><br>　0：削除なし，1：削除あり<br>
                      <b>【口座種別(普通/当座)】</b><br>　0：普通，1：当座<br>
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
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal01">↑　アップロード</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">顧客情報のCSVアップロード</h4>
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

<script type="text/javascript">
$(function () {
  $('[data-toggle="popover"]').popover({
	  html:1
  })
})
</script>

<!-- </form> -->

<hr>

{form_open('/data_csvup/customer_csvdown/' , 'name="dataForm" class="form-horizontal"')}

  <div class="form-group form-group-lg">
    <label for="cl_detail" class="col-xs-4 col-md-4 control-label">【顧客情報CSV：ダウンロード】</label>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <br>・顧客情報の全件をダウンロードします。
      <br>・max.500 の制限をかけています。
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      {$dl_mess}
    </div>
  </div>

  <br><br>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal02">↓　ダウンロード</button>
    </div>
  </div>

  <br>
  <!-- Button trigger modal -->
  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">顧客情報のCSVダウンロード</h4>
        </div>
        <div class="modal-body">
          <p>ダウンロードしますか。&hellip;</p>
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
