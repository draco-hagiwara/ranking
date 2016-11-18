{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

  <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　請求書情報　更新</p></H3>

{form_open('/invoicelist/detailchk/' , 'name="customerDetailForm" class="form-horizontal h-adr"')}

  {$mess}
  <div class="form-group">
    <label for="cm_status" class="col-xs-2 col-md-2 control-label">ステータス選択</label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('iv_status', $options_iv_status, set_value('iv_status', $info.iv_status))}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_slip_no" class="col-xs-2 col-md-2 control-label">請求書NO</label>
    <div class="col-md-8">{$info.iv_slip_no}</div>
  </div>
  <div class="form-group">
    <label for="iv_issue_date" class="col-xs-2 col-md-2 control-label">発行日</label>
    <div class="col-md-8">{$info.iv_issue_date}</div>
  </div>
  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">振込期日</label>
    <div class="col-md-8">{$info.iv_pay_date}</div>
  </div>
  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-2 col-md-2 control-label">送付先住所</label>
    <div class="col-md-8">〒{$info.iv_zip01}-{$info.iv_zip02}</div>
    <div class="col-md-8">{$info.iv_pref} {$info.iv_addr01} {$info.iv_addr02} {$info.iv_buil}</div><br><br>
    <div class="col-md-8 col-md-offset-2">{$info.iv_company}</div>
    <div class="col-md-8 col-md-offset-2">{$info.iv_department}</div><br>
    <div class="col-md-8 col-md-offset-2">{$info.iv_person01} {$info.iv_person02}</div>
  </div>
  <div class="form-group">
    <label for="iv_bank_cd" class="col-xs-2 col-md-2 control-label">銀行情報</label>
    <div class="col-md-8">（{$info.iv_bank_cd}）{$info.iv_bank_nm}</div>
    <div class="col-md-8">（{$info.iv_branch_cd}）{$info.iv_branch_nm}</div>
    <div class="col-md-8 col-md-offset-2">（{$info.iv_account_no}）{$info.iv_account_nm}</div>
  </div>





  <table class="table table-hover table-bordered">
    <tbody>
      <tr class="active">
        <td class="col-md-7 text-center">請　求　項　目</td>
        <td class="col-md-1 text-center">数 量</td>
        <td class="col-md-1 text-center">単 価（円）</td>
        <td class="col-md-1 text-center">金 額（円）</td>
      </tr>

      {foreach from=$infodetail item=ivd}
      <tr>
        <td class="col-md-7 input-group-sm">
          対象キーワード：「{$ivd.ivd_item}」
        </td>
        <td class="col-md-1 input-group-sm text-center">
          {$ivd.ivd_qty|number_format}
        </td>
        <td class="col-md-1 input-group-sm text-right">
          {$ivd.ivd_price|number_format}
        </td>
        <td class="col-md-1 input-group-sm text-right">
          {$ivd.ivd_total|number_format}
        </td>
      </tr>
      {/foreach}

      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input('ivd_item0' , set_value('ivd_item0','') , 'class="form-control" placeholder="キーワード文字のみ入力してください。"')}
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
          {form_input('ivd_item1' , set_value('ivd_item1','') , 'class="form-control" placeholder="キーワード文字のみ入力してください。"')}
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
    <label for="iv_remark" class="col-sm-2 control-label">請求書：備考</label>
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
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>（履歴ファイルが作成されます）
  </div>
  </div>
  {/if}

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
