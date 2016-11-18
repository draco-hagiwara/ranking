{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>
  {*https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js*}
  {*<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>*}

  <script type="text/javascript" src="https://{$smarty.server.SERVER_NAME}/js/jQuery.jPrintArea.js"></script>{* 印刷プレビュー表示 *}
{* 印刷プレビュー表示 *}
<script type="text/javascript">
$(function(){
  $('#btn_print1').click(function(){
    $.jPrintArea(".print-area");
  });

  $('#btn_print2').click(function(){
    $.jPrintArea(".print-area");
  });
});
</script>


  <script type="text/javascript" src="https://{$smarty.server.SERVER_NAME}/js/printThis.js"></script>{* 印刷プレビュー表示 *}

  <script type="text/javascript" src="https://{$smarty.server.SERVER_NAME}/js/jquery.printelement.min.js"></script>{* 印刷プレビュー表示 *}


{* ヘッダー部分　END *}



<body>

<H3><p class="bg-success">　　PDF作成</p></H3>


{form_open('graph/pdf_javascript/' , 'name="pdfForm" class="form-horizontal"')}

  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">PDF作成 (javascript)</button>
    </div>
  </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ＰＤＦ　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='pdf' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<br><br>
{form_open('graph/pdf_font/' , 'name="pdfForm" class="form-horizontal"')}

  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal02">PDF作成 (font)</button>
    </div>
  </div>

  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ＰＤＦ　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='pdf' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<br><br>
{form_open('graph/pdf_invoice/' , 'name="pdfForm" class="form-horizontal"')}

  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal03">PDF作成 (Invoice)</button>
    </div>
  </div>

  <div class="modal fade" id="myModal03" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ＰＤＦ　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='pdf' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<br><br>
{* 各種テスト用：http://www.monzen.org/Refdoc/tcpdf/ *}
{form_open('graph/pdf_demo/' , 'name="pdfForm" class="form-horizontal"')}

  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal04">PDF作成 (demo)</button>
    </div>
  </div>

  <div class="modal fade" id="myModal04" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ＰＤＦ　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='pdf' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<br><br>
{* HTMLテスト用：http://phpjp.info/?TCPDF%E3%82%92%E4%BD%BF%E3%81%A3%E3%81%A6HTML%E3%82%92PDF%E3%81%AB *}
{form_open('graph/pdf_html/' , 'name="pdfForm" class="form-horizontal"')}

  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal05">PDF作成 (HTML請求書)</button>
    </div>
  </div>

  <div class="modal fade" id="myModal05" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ＰＤＦ　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='pdf' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<br><br><br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
