{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　支払情報　更新</p></H3>

<!-- <form> -->

{form_open('/shokaifeelist/detailchk/' , 'name="detailForm" class="form-horizontal h-adr"')}

  {$mess}
  <div class="form-group">
    <label for="skf_status" class="col-xs-2 col-md-2 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('skf_status', $options_skf_status, set_value('skf_status', $info.skf_status))}
      {if form_error('skf_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="skf_pay_no" class="col-xs-2 col-md-2 control-label">支払通知書発行NO</label>
    <div class="col-md-8">{$info.skf_pay_no}</div>
  </div>
  <div class="form-group">
    <label for="skf_pay_yymm" class="col-xs-2 col-md-2 control-label">支払月度</label>
    <div class="col-md-8">{$info.skf_pay_yymm|substr:0:4}年{$info.skf_pay_yymm|substr:4:2}月</div>
  </div>
  <div class="form-group">
    <label for="skf_issue_date" class="col-xs-2 col-md-2 control-label">発行日</label>
    <div class="col-md-8">{$info.skf_issue_date}</div>
  </div>
  <div class="form-group">
    <label for="skf_pay_date" class="col-xs-2 col-md-2 control-label">振込日</label>
    <div class="col-md-8">{$info.skf_pay_date}</div>
  </div>
  <div class="form-group">
    <label for="skf_pay_total" class="col-xs-2 col-md-2 control-label">振込金額</label>
    <div class="col-md-8">{$info.skf_pay_total|number_format} 円</div>
  </div>
  <div class="form-group">
    <label for="skf_pay_tax" class="col-xs-2 col-md-2 control-label">振込金額消費税</label>
    <div class="col-md-8">{$info.skf_pay_tax|number_format} 円</div>
  </div>
  <div class="form-group">
    <label for="skf_payment" class="col-xs-2 col-md-2 control-label">支払サイト</label>
    <div class="col-md-8">{$options_skf_payment}</div>
  </div>
  <div class="form-group">
    <label for="skf_sk_company" class="col-xs-2 col-md-2 control-label">支払先会社名</label>
    <div class="col-md-8">{$info.skf_sk_company}</div>
  </div>
  <div class="form-group">
    <label for="skf_account_nm" class="col-xs-2 col-md-2 control-label">振込人名義</label>
    <div class="col-md-8">{$info.skf_account_nm}</div>
  </div>

  <div class="form-group">
  <table class="table table-hover table-bordered">
    <tbody>
      <tr class="active text-danger">
        <td class="col-xs-1 text-center"><h6>売上先会社名</h6></td>
        <td class="col-xs-1 text-center"><h6>売上金額(円)</h6></td>
        <td class="col-xs-1 text-center"><h6>紹介料固定(円)</h6></td>
        <td class="col-xs-1 text-center"><h6>紹介料率</h6></td>
        <td class="col-xs-1 text-center"><h6>紹介料(円)</h6></td>
        <td class="col-xs-1 text-center"><h6>対象KW</h6></td>
        <td class="col-xs-1 text-center"><h6>請求書NO</h6></td>
      </tr>
    {foreach from=$infodetail item=skd name="no"}
      {form_hidden("skd_seq[{$smarty.foreach.no.iteration}]" , $skd.skd_seq)}
      <tr>
        <td class="col-xs-1 input-group-xs"><h6>{$skd.skd_sa_company}</h6></td>
        <td class="col-xs-1 input-group-xs text-center"><h6>{$skd.skd_sa_total|number_format}</h6></td>
          {form_hidden("skd_sa_total[{$smarty.foreach.no.iteration}]", $skd.skd_sa_total)}

        {if $info.skf_status==0}
          <td class="col-md-1 input-group-sm">
            {form_input("skd_pay_fix[{$smarty.foreach.no.iteration}]" , set_value('skd_pay_fix', $skd.skd_pay_fix) , 'class="form-control text-center"')}
            {if form_error('skd_pay_fix')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skd_pay_fix')}</font></label>{/if}
          </td>
          <td class="col-md-1 input-group-sm">
            {form_input("skd_pay_rate[{$smarty.foreach.no.iteration}]" , set_value('skd_pay_rate', $skd.skd_pay_rate) , 'class="form-control text-center"')}
            {if form_error('skd_pay_rate')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skd_pay_rate')}</font></label>{/if}
          </td>
        {elseif $info.skf_status==1}
          <td class="col-xs-1 input-group-xs text-center"><h6>{$skd.skd_pay_fix|number_format}</h6></td>
          <td class="col-xs-1 input-group-xs text-center"><h6>{$skd.skd_pay_rate}</h6></td>
        {/if}

        <td class="col-xs-1 input-group-xs text-center"><h6>{$skd.skd_pay_subtotal|number_format}</h6></td>
        <td class="col-xs-1 input-group-xs"><h6>{$skd.skd_sa_keyword}</h6></td>
        <td class="col-xs-1 input-group-xs text-center"><h6>{$skd.skd_sa_slip_no}</h6></td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  </div>

  <div class="form-group">
    <label for="skf_remark" class="col-sm-2 control-label">支払通知書：備考<br>(改行禁止。31文字)</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="skf_remark" name="skf_remark" placeholder="max.31文字。改行禁止。">{$info.skf_remark}</textarea>
      {if form_error('skf_remark')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_remark')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="skf_memo" class="col-sm-2 control-label">メ　　　　モ</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="skf_memo" name="skf_memo" placeholder="max.1000文字">{$info.skf_memo}</textarea>
      {if form_error('skf_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('skf_memo')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('skf_seq', $info.skf_seq)}
  {form_hidden('skf_status_old', $info.skf_status)}
  {form_hidden('skf_pay_tax', $info.skf_pay_tax)}

  <!-- Button trigger modal -->
  <div class="row">
  <div class="col-sm-4 col-sm-offset-2">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">更新する</button>
  </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">支払情報　更新</h4>
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

<!-- </form> -->


<!-- <form> -->

{form_open('/pdf_shokai/pdf_one/' , 'name="pdfForm" class="form-horizontal h-adr"')}

  {form_hidden('skf_seq', $info.skf_seq)}

  <!-- Button trigger modal -->
  <div class="col-sm-2 col-sm-offset-2">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal02">支払通知書(PDF)作成</button>
  </div>
  </div>

  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">支払通知書PDF　作成</h4>
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
