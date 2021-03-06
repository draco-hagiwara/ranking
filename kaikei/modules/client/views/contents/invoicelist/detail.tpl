{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　請求書情報　更新</p></H3>

<!-- <form> -->

{form_open('/invoicelist/detailchk/' , 'name="detailForm" class="form-horizontal h-adr"')}

  {$mess}
  <div class="form-group">
    <label for="cm_status" class="col-xs-2 col-md-2 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('iv_status', $options_iv_status, set_value('iv_status', $info.iv_status))}
      {if form_error('iv_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_slip_no" class="col-xs-2 col-md-2 control-label">請求書NO</label>
    <div class="col-md-8">{$info.iv_slip_no}</div>
  </div>
  <div class="form-group">
    <label for="iv_sales_yymm" class="col-xs-2 col-md-2 control-label">売上月度</label>
    <div class="col-md-8">{$info.iv_sales_yymm}</div>
  </div>

  {if $info.iv_status==0}
    <div class="form-group">
    <label for="iv_issue_date" class="col-xs-2 col-md-2 control-label">発行日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_issue_date' , set_value('iv_issue_date', {$info.iv_issue_date}) , 'id="mydate1" class="form-control"')}
      {if form_error('iv_issue_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_date')}</font></label>{/if}
    </div>
    <div class="col-md-5">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">振込期日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_pay_date' , set_value('iv_pay_date', {$info.iv_pay_date}) , 'id="mydate2" class="form-control"')}
      {if form_error('iv_pay_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_pay_date')}</font></label>{/if}
    </div>
  </div>
  {else}
  <div class="form-group">
    <label for="iv_issue_date" class="col-xs-2 col-md-2 control-label">発行日</label>
    <div class="col-md-8">{$info.iv_issue_date}</div>
  </div>
  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">振込期日</label>
    <div class="col-md-8">{$info.iv_pay_date}</div>
  </div>
  {form_hidden('iv_issue_date', {$info.iv_issue_date})}
  {form_hidden('iv_pay_date',   {$info.iv_pay_date})}
  {/if}

  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">送付先住所</label>
    <div class="col-md-8">〒{$info.iv_zip01}-{$info.iv_zip02}</div>
    <div class="col-md-8">{$info.iv_pref} {$info.iv_addr01} {$info.iv_addr02} {$info.iv_buil}</div>
    <br><br><br>
    <div class="col-md-8 col-md-offset-2">{$info.iv_company}</div>
    <div class="col-md-8 col-md-offset-2">　{$info.iv_department}</div><br>
    <div class="col-md-8 col-md-offset-2">　{$info.iv_person01} {$info.iv_person02}</div>
  </div>

{*
  <div class="form-group">
    <label for="iv_bank_cd" class="col-xs-2 col-md-2 control-label">銀行情報</label>
    <div class="col-md-8">（{$info.iv_bank_cd}）{$info.iv_bank_nm}</div>
    <div class="col-md-8">（{$info.iv_branch_cd}）{$info.iv_branch_nm}</div>
    <div class="col-md-8 col-md-offset-2">（{$info.iv_account_no}）{$info.iv_account_nm}</div>
  </div>
*}

  <table class="table table-hover table-bordered">
    <tbody>
      <tr class="active">
        <td class="col-md-7 text-center">請　求　項　目　（二段：対象URL，三段：ランクイン範囲）</td>
        <td class="col-md-1 text-center">数量or日数</td>
        <td class="col-md-1 text-center">単 価（円）</td>
        <td class="col-md-1 text-center">金 額（円）</td>
      </tr>

      {foreach from=$infodetail item=ivd  name="no"}
        {form_hidden("seq{$smarty.foreach.no.iteration}" , $ivd.ivd_seq)}
      <tr>
        <td class="col-md-7 input-group-sm">
          {if $ivd.ivd_item_cmseq}{$ivd.ivd_item_cmseq} => {/if}
          {if $ivd.ivd_iv_accounting==0}SEO固定：対象キーワード：
          {elseif $ivd.ivd_iv_accounting==1}固定：対象キーワード：
          {elseif $ivd.ivd_iv_accounting==2}<font color=red>成果</font>：対象キーワード：
          {elseif $ivd.ivd_iv_accounting==3}<font color=red>固+成</font>：対象キーワード：
          {else}{/if}
          「{$ivd.ivd_item}」
        </td>

        {if $info.iv_status==0}
          <td class="col-md-1 input-group-sm">
            {form_input("qty{$smarty.foreach.no.iteration}" , set_value('ivd_qty', $ivd.ivd_qty|number_format) , 'class="form-control text-center"')}
            {if form_error('ivd_qty')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_qty')}</font></label>{/if}
          </td>
        {else}
          <td class="col-md-1 input-group-sm text-center">
            {$ivd.ivd_qty|number_format}
            {form_hidden("qty{$smarty.foreach.no.iteration}"  , $ivd.ivd_qty)}
          </td>
        {/if}
        <td class="col-md-1 input-group-sm text-right">
          {$ivd.ivd_price|number_format}
        </td>
        <td class="col-md-1 input-group-sm text-right">
          {$ivd.ivd_total|number_format}
        </td>

      </tr>
      <tr>
        <td class="col-md-7 input-group-sm">
          　{$ivd.ivd_item_url}
        </td>
        <td colspan="3" class="col-md-1"></td>
      </tr>
      {if $ivd.ivd_item_comment!=""}
      <tr>
        <td class="col-md-7 input-group-sm">
          　{$ivd.ivd_item_comment}
        </td>
        <td colspan="3" class="col-md-1"></td>
      </tr>
      {/if}
      {/foreach}

      {if $info.iv_status==0}
      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input('ivd_item0' , set_value('ivd_item0','') , 'class="form-control" placeholder="追加キーワード文字を入力してください。"')}
          {if form_error('ivd_item0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_item0')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_qty0' , set_value('ivd_qty0', '') , 'class="form-control text-center"')}
          {if form_error('ivd_qty0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_qty0')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_price0' , set_value('ivd_price0', '') , 'class="form-control text-right"')}
          {if form_error('ivd_price0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_price0')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_total0' , set_value('ivd_total0', 0) , 'class="form-control text-right"')}
          {if form_error('ivd_total0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_total0')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input("ivd_item_url0" , set_value("ivd_item_url0", '') , 'class="form-control" placeholder="追加対象URLを入力してください。"')}
          {if form_error("ivd_item_url0")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("ivd_item_url0")}</font></label>{/if}
        </td>
        <td colspan="3" class="col-md-1"></td>
      </tr>

      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input('ivd_item1' , set_value('ivd_item1','') , 'class="form-control" placeholder="追加キーワード文字を入力してください。"')}
          {if form_error('ivd_item1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_item1')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_qty1' , set_value('ivd_qty1', '') , 'class="form-control text-center"')}
          {if form_error('ivd_qty1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_qty1')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_price1' , set_value('ivd_price1', '') , 'class="form-control text-right"')}
          {if form_error('ivd_price1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_price1')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_total1' , set_value('ivd_total1', 0) , 'class="form-control text-right"')}
          {if form_error('ivd_total1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_total1')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input("ivd_item_url1" , set_value("ivd_item_url1", '') , 'class="form-control" placeholder="追加対象URLを入力してください。"')}
          {if form_error("ivd_item_url1")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("ivd_item_url1")}</font></label>{/if}
        </td>
        <td colspan="3" class="col-md-1"></td>
      </tr>
      {else}
        {form_hidden('ivd_item0' , "")}
        {form_hidden('ivd_qty0'  , "")}
        {form_hidden('ivd_price0', "")}
        {form_hidden('ivd_total0', 0)}
        {form_hidden('ivd_item_url0' , "")}
        {form_hidden('ivd_item1' , "")}
        {form_hidden('ivd_qty1'  , "")}
        {form_hidden('ivd_price1', "")}
        {form_hidden('ivd_total1', 0)}
        {form_hidden('ivd_item_url1' , "")}
      {/if}

      <tr>
        <td colspan="3" class="input-group-sm text-right">小計</td>
        <td class="col-md-1 input-group-sm text-right">{$info.iv_subtotal|number_format}</td>
      </tr>
      <tr>
        <td colspan="3" class="input-group-sm text-right">消費税等</td>
        <td class="col-md-1 input-group-sm text-right">{$info.iv_tax|number_format}</td>
      </tr>
      <tr>
        <td colspan="3" class="input-group-sm text-right">合計</td>
        <td class="col-md-1 input-group-sm text-right">{$info.iv_total|number_format}</td>
      </tr>
    </tbody>
  </table>

  <div class="form-group">
    <label for="iv_remark" class="col-sm-2 control-label">請求書：備考<br>(max.4行)</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="iv_remark" name="iv_remark" placeholder="max.100文字">{$info.iv_remark}</textarea>
      {if form_error('iv_remark')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_remark')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_memo" class="col-sm-2 control-label">メ　　　　モ</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="iv_memo" name="iv_memo" placeholder="max.1000文字">{$info.iv_memo}</textarea>
      {if form_error('iv_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_memo')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('iv_seq', $info.iv_seq)}

  <!-- Button trigger modal -->
  {if $info.iv_status!=9}
  <div class="row">
  <div class="col-sm-4 col-sm-offset-2">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">更新する</button>（履歴ファイルが作成されます）
  </div>
  {/if}

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">請求書情報　更新</h4>
        </div>
        <div class="modal-body">
          <p>更新しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
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

<!-- </form> -->


<!-- <form> -->

{form_open('/pdf_create/pdf_one/' , 'name="pdfForm" class="form-horizontal h-adr"')}
{*form_open('/pdf_create/pdf_one/' , 'name="pdfForm" class="form-horizontal h-adr" target="_blank"')*}

  {form_hidden('iv_seq', $info.iv_seq)}

  <!-- Button trigger modal -->
  {if $info.iv_status!=9}
  <div class="col-sm-2 col-sm-offset-2">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal02">請求書(PDF)作成</button>
  </div>
  </div>
  {/if}

  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">請求書PDF　作成</h4>
        </div>
        <div class="modal-body">
          <p>作成しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
          <button type='submit' name='submit' value='cancel' class="btn btn-sm btn-primary">キャンセル</button>
          {*<button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>*}
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
