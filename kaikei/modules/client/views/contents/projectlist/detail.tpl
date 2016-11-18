{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　アカウント情報　更新</p></H3>

{form_open('/projectlist/detailchk/' , 'name="accountDetailForm" class="form-horizontal"')}

  {$mess}
    <div class="form-group">
    <label for="pj_cm_company" class="col-sm-3 control-label">会社名<font color=red> *</font></label>
    <div class="col-xs-8 col-md-8">
      {$info.pj_cm_company}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_status" class="col-sm-3 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('pj_status', $options_pj_status, set_value('pj_status', $info.pj_status))}
      {if form_error('pj_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_invoice_status" class="col-sm-3 control-label">請求書発行ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('pj_invoice_status', $options_pj_iv_type, set_value('pj_invoice_status', $info.pj_invoice_status))}
      {if form_error('pj_invoice_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_invoice_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_contract" class="col-xs-3 col-md-3 control-label">契約期間<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_start_date' , set_value('pj_start_date', $info.pj_start_date) , 'id="mydate1" class="form-control" placeholder="開始日"')}
      {if form_error('pj_start_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_start_date')}</font></label>{/if}
    </div>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_end_date' , set_value('pj_end_date', $info.pj_end_date) , 'id="mydate2" class="form-control" placeholder="終了日"')}
      {if form_error('pj_end_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_end_date')}</font></label>{/if}
    </div>
    <div class="col-md-5">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
      {if $err_date==TRUE}<span class="label label-danger">Error : </span><label><font color=red>「契約期間」欄で入力した日付が不整合です。</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_keyword" class="col-xs-3 col-md-3 control-label">検索キーワード<font color=red> *</font></label>
    <div class="col-xs-8 col-md-8">
      {form_input('pj_keyword' , set_value('pj_keyword', $info.pj_keyword) , 'class="form-control" placeholder="検索キーワードを入力してください。max.100文字"')}
      {if form_error('pj_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_keyword')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_url" class="col-xs-3 col-md-3 control-label">対象URL<font color=red> *</font></label>
    <div class="col-xs-8 col-md-8">
      {form_input('pj_url' , set_value('pj_url', $info.pj_url) , 'class="form-control" placeholder="対象URLを入力してください。max.100文字"')}
      {if form_error('pj_url')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_url')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_target" class="col-xs-3 col-md-3 control-label">順位取得対象<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
    </div>
  </div>
  <div class="form-group">
    <label for="pj_language" class="col-xs-3 col-md-3 control-label">対象言語<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
    </div>
  </div>
  <div class="form-group">
    <label for="pj_accounting" class="col-xs-3 col-md-3 control-label">課金方式<font color=red> *</font></label>
    <div class="radio">
      <label class="col-xs-2 col-md-2 control-label">
        <input type="radio" name="pj_accounting" id="optionsRadios1" value="0" {if $info.pj_accounting==0}checked{/if}>固定
      </label>
      <label class="col-xs-2 col-md-2 control-label">
        <input type="radio" name="pj_accounting" id="optionsRadios2" value="1" {if $info.pj_accounting==1}checked{/if}>成果報酬
      </label>
      <label class="col-xs-2 col-md-2 control-label">
        <input type="radio" name="pj_accounting" id="optionsRadios3" value="2" {if $info.pj_accounting==2}checked{/if}>固定+成果
      </label>
    </div>
  </div>
  <div class="form-group">
    <label for="pj_url_match" class="col-xs-3 col-md-3 control-label">URL一致方式<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
    </div>
  </div>
  <div class="form-group">
    <label for="pj_billing" class="col-xs-3 col-md-3 control-label">固定請求金額<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('pj_billing' , set_value('pj_billing', $info.pj_billing) , 'class="form-control"')}
      {if form_error('pj_billing')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_billing')}</font></label>{/if}
    </div>
    <div class="col-xs-1 col-md-1">
      <p class="redText">円</p>
    </div>
  </div>
  <div class="form-group">
    <label for="pj_salesman" class="col-md-3 control-label">担当営業<font color=red> *</font></label>
    <div class="col-md-2 btn-lg">
      {form_dropdown('pj_salesman', $options_pj_salesman, set_value('pj_salesman', $info.pj_salesman))}
      {if form_error('pj_salesman')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_salesman')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_memo" class="col-sm-3 control-label">備　　考</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="pj_memo" name="pj_memo" placeholder="max.1000文字">{$info.pj_memo}</textarea>
      {if form_error('pj_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_memo')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="pj_tag" class="col-xs-3 col-md-3 control-label">タグ設定</label>
    <div class="col-xs-8 col-md-8">
      {form_input('pj_tag' , set_value('pj_tag', $info.pj_tag) , 'class="form-control" placeholder="タグを入力してください。max.100文字"')}
      {if form_error('pj_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('pj_tag')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('pj_seq', $info.pj_seq)}
  {form_hidden('pj_cm_seq', $info.pj_cm_seq)}
  {form_hidden('pj_cm_company', $info.pj_cm_company)}

  <br><br>
  <!-- Button trigger modal -->
  <div class="row">
  <div class="col-sm-4 col-sm-offset-3">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>
  </div>
  </div>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">受注案件情報　更新</h4>
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

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
