{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>

  <link rel="stylesheet" type="text/css" href="{base_url()}../../css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="{base_url()}../../js/bootstrap-datepicker.min.js"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　請求書データ　新規作成</p></H3>

{form_open('invo_create/add_iv/' , 'name="clientForm" class="form-horizontal h-adr"')}
  {$mess}
  <div class="form-group">
    <label for="iv_status" class="col-sm-3 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('iv_status', $options_iv_status, set_value('iv_status', ''))}
      {if form_error('iv_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_accounting" class="col-sm-3 control-label">課金方式選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('iv_accounting', $options_iv_accounting, set_value('iv_accounting', ''))}
      {if form_error('iv_accounting')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_accounting')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_issue_yymm" class="col-sm-3 control-label">発行年月選択</label>
    <div class="col-sm-2 input-lg">
      {form_dropdown('iv_issue_yymm', $options_date_fix, set_value('iv_issue_yymm', {$info.iv_issue_yymm}))}
      {if form_error('iv_issue_yymm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_yymm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_issue_date" class="col-xs-3 col-md-3 control-label">発効日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_issue_date' , set_value('iv_issue_date', '') , 'id="mydate1" class="form-control"')}
      {if form_error('iv_issue_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_issue_date')}</font></label>{/if}
    </div>
    <div class="col-md-5">
      <p class="redText"><small>※入力フォーマット（ yyyy/dd/mm　または　yyyy-dd-mm ）</small></p>
    </div>
  </div>
  <div class="form-group">
    <label for="iv_pay_date" class="col-xs-3 col-md-3 control-label">振込期日指定<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2">
      {form_input('iv_pay_date' , set_value('iv_pay_date', '') , 'id="mydate2" class="form-control"')}
      {if form_error('iv_pay_date')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_pay_date')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_collect" class="col-sm-3 control-label">回収サイト</label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('iv_collect', $options_iv_collect, set_value('iv_collect', $info.iv_collect))}
      {if form_error('iv_collect')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_collect')}</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="iv_company_cm" class="col-md-3 control-label">会社名<font color=red> *</font></label>
    <div class="col-md-8">{$info.iv_company_cm}</div>
  </div>
  <div class="form-group">
    <label for="iv_company" class="col-xs-3 col-md-3 control-label">請求書住所：会社名<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('iv_company' , set_value('iv_company', {$info.iv_company}) , 'class="form-control p-locality" placeholder="請求書住所：会社名を入力してください。 max.50文字"')}
      {if form_error('iv_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_company')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_zip" class="col-xs-3 col-md-3 control-label">郵便番号<font color=red> *</font></label>
    <div class="col-xs-3 col-md-2">
      <span class="p-country-name" style="display:none;">Japan</span>
      {form_input('iv_zip01' , set_value('iv_zip01', {$info.iv_zip01}) , 'class="form-control p-postal-code" placeholder="郵便番号（3ケタ）"')}
      {if form_error('iv_zip01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_zip01')}</font></label>{/if}
    </div>
    <div class="col-xs-3 col-md-2">
      {form_input('iv_zip02' , set_value('iv_zip02', {$info.iv_zip02}) , 'class="form-control p-postal-code" placeholder="郵便番号（4ケタ）"')}
      {if form_error('iv_zip02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_zip02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_pref" class="col-xs-3 col-md-3 control-label">都道府県<font color=red> *</font></label>
    <div class="col-xs-3 col-md-2 btn-lg">
        <select name="iv_pref" class="p-region">
            <option value=""> -- 選択してください -- </option>
            <option value="北海道" {if $info.iv_pref=="北海道"}selected{/if}>北海道</option>
            <option value="青森県" {if $info.iv_pref=="青森県"}selected{/if}>青森県</option>
            <option value="岩手県" {if $info.iv_pref=="岩手県"}selected{/if}>岩手県</option>
            <option value="宮城県" {if $info.iv_pref=="宮城県"}selected{/if}>宮城県</option>
            <option value="秋田県" {if $info.iv_pref=="秋田県"}selected{/if}>秋田県</option>
            <option value="山形県" {if $info.iv_pref=="山形県"}selected{/if}>山形県</option>
            <option value="福島県" {if $info.iv_pref=="福島県"}selected{/if}>福島県</option>
            <option value="茨城県" {if $info.iv_pref=="茨城県"}selected{/if}>茨城県</option>
            <option value="栃木県" {if $info.iv_pref=="栃木県"}selected{/if}>栃木県</option>
            <option value="群馬県" {if $info.iv_pref=="群馬県"}selected{/if}>群馬県</option>
            <option value="埼玉県" {if $info.iv_pref=="埼玉県"}selected{/if}>埼玉県</option>
            <option value="千葉県" {if $info.iv_pref=="千葉県"}selected{/if}>千葉県</option>
            <option value="東京都" {if $info.iv_pref=="東京都"}selected{/if}>東京都</option>
            <option value="神奈川県" {if $info.iv_pref=="神奈川県"}selected{/if}>神奈川県</option>
            <option value="新潟県" {if $info.iv_pref=="新潟県"}selected{/if}>新潟県</option>
            <option value="富山県" {if $info.iv_pref=="富山県"}selected{/if}>富山県</option>
            <option value="石川県" {if $info.iv_pref=="石川県"}selected{/if}>石川県</option>
            <option value="福井県" {if $info.iv_pref=="福井県"}selected{/if}>福井県</option>
            <option value="山梨県" {if $info.iv_pref=="山梨県"}selected{/if}>山梨県</option>
            <option value="長野県" {if $info.iv_pref=="長野県"}selected{/if}>長野県</option>
            <option value="岐阜県" {if $info.iv_pref=="岐阜県"}selected{/if}>岐阜県</option>
            <option value="静岡県" {if $info.iv_pref=="静岡県"}selected{/if}>静岡県</option>
            <option value="愛知県" {if $info.iv_pref=="愛知県"}selected{/if}>愛知県</option>
            <option value="三重県" {if $info.iv_pref=="三重県"}selected{/if}>三重県</option>
            <option value="滋賀県" {if $info.iv_pref=="滋賀県"}selected{/if}>滋賀県</option>
            <option value="京都府" {if $info.iv_pref=="京都府"}selected{/if}>京都府</option>
            <option value="大阪府" {if $info.iv_pref=="大阪府"}selected{/if}>大阪府</option>
            <option value="兵庫県" {if $info.iv_pref=="兵庫県"}selected{/if}>兵庫県</option>
            <option value="奈良県" {if $info.iv_pref=="奈良県"}selected{/if}>奈良県</option>
            <option value="和歌山県" {if $info.iv_pref=="和歌山県"}selected{/if}>和歌山県</option>
            <option value="鳥取県" {if $info.iv_pref=="鳥取県"}selected{/if}>鳥取県</option>
            <option value="島根県" {if $info.iv_pref=="島根県"}selected{/if}>島根県</option>
            <option value="岡山県" {if $info.iv_pref=="岡山県"}selected{/if}>岡山県</option>
            <option value="広島県" {if $info.iv_pref=="広島県"}selected{/if}>広島県</option>
            <option value="山口県" {if $info.iv_pref=="山口県"}selected{/if}>山口県</option>
            <option value="徳島県" {if $info.iv_pref=="徳島県"}selected{/if}>徳島県</option>
            <option value="香川県" {if $info.iv_pref=="香川県"}selected{/if}>香川県</option>
            <option value="愛媛県" {if $info.iv_pref=="愛媛県"}selected{/if}>愛媛県</option>
            <option value="高知県" {if $info.iv_pref=="高知県"}selected{/if}>高知県</option>
            <option value="福岡県" {if $info.iv_pref=="福岡県"}selected{/if}>福岡県</option>
            <option value="佐賀県" {if $info.iv_pref=="佐賀県"}selected{/if}>佐賀県</option>
            <option value="長崎県" {if $info.iv_pref=="長崎県"}selected{/if}>長崎県</option>
            <option value="熊本県" {if $info.iv_pref=="熊本県"}selected{/if}>熊本県</option>
            <option value="大分県" {if $info.iv_pref=="大分県"}selected{/if}>大分県</option>
            <option value="宮崎県" {if $info.iv_pref=="宮崎県"}selected{/if}>宮崎県</option>
            <option value="鹿児島県" {if $info.iv_pref=="鹿児島県"}selected{/if}>鹿児島県</option>
            <option value="沖縄県" {if $info.iv_pref=="沖縄県"}selected{/if}>沖縄県</option>
        </select>
      {if form_error('iv_pref')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_pref')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_addr01" class="col-md-3 control-label">市区町村<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('iv_addr01' , set_value('iv_addr01', {$info.iv_addr01}) , 'class="form-control p-locality" placeholder="市区町村を入力してください。 max.100文字"')}
      {if form_error('iv_addr01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_addr01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_addr02" class="col-md-3 control-label">町名・番地<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('iv_addr02' , set_value('iv_addr02', {$info.iv_addr02}) , 'class="form-control p-street-address" placeholder="町名・番地を入力してください。 max.100文字"')}
      {if form_error('iv_addr02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_addr02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_buil" class="col-md-3 control-label">ビル・マンション名など</label>
    <div class="col-md-8">
      {form_input('iv_buil' , set_value('iv_buil', {$info.iv_buil}) , 'class="form-control p-extended-address" placeholder="ビル・マンション名などを入力してください。 max.100文字"')}
      {if form_error('iv_buil')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_buil')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_department" class="col-md-3 control-label">担当所属部署</label>
    <div class="col-md-8">
      {form_input('iv_department' , set_value('iv_department', {$info.iv_department}) , 'class="form-control" placeholder="所属部署を入力してください。max.50文字"')}
      {if form_error('iv_department')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_department')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_person" class="col-md-3 control-label">担当者<font color=red> *</font></label>
    <div class="col-md-4">
      {form_input('iv_person01' , set_value('iv_person01', {$info.iv_person01}) , 'class="form-control" placeholder="担当者姓を入力してください。max.50文字"')}
      {if form_error('iv_person01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_person01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('iv_person02' , set_value('iv_person02', {$info.iv_person02}) , 'class="form-control" placeholder="担当者名を入力してください。max.50文字"')}
      {if form_error('iv_person02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_person02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_memo_iv" class="col-sm-3 control-label">備　考　欄<br>(max.4行)</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="iv_remark" name="iv_remark" placeholder="max.100文字">{$tmp_remark}</textarea>
      {if form_error('iv_remark')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_remark')}</font></label>{/if}
    </div>
  </div>

{*
  <div class="form-group">
    <label for="iv_bank_cd" class="col-xs-3 col-md-3 control-label">銀　　　　行<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('iv_bank_cd' , set_value('iv_bank_cd', {$info.iv_bank_cd}) , 'class="form-control" placeholder="銀行CDを入力してください"')}
      {if form_error('iv_bank_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_bank_cd')}</font></label>{/if}
    </div>
    <div class="col-xs-4 col-md-4">
      {form_input('iv_bank_nm' , set_value('iv_bank_nm', {$info.iv_bank_nm}) , 'class="form-control" placeholder="銀行名を入力してください"')}
      {if form_error('iv_bank_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_bank_nm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_branch_cd" class="col-xs-3 col-md-3 control-label">支　　　　店<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('iv_branch_cd' , set_value('iv_branch_cd', {$info.iv_branch_cd}) , 'class="form-control" placeholder="支店CDを入力してください"')}
      {if form_error('iv_branch_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_branch_cd')}</font></label>{/if}
    </div>
    <div class="col-xs-4 col-md-4">
      {form_input('iv_branch_nm' , set_value('iv_branch_nm', {$info.iv_branch_nm}) , 'class="form-control" placeholder="支店名を入力してください"')}
      {if form_error('iv_branch_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_branch_nm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_kind" class="col-sm-3 control-label">口座種別選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('iv_kind', $options_iv_kind, set_value('iv_kind', {$info.iv_kind}))}
      {if form_error('iv_kind')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_kind')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_account_no" class="col-xs-3 col-md-3 control-label">口　　　　座<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('iv_account_no' , set_value('iv_account_no', {$info.iv_account_no}) , 'class="form-control" placeholder="口座番号を入力してください"')}
      {if form_error('iv_account_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_account_no')}</font></label>{/if}
    </div>
    <div class="col-xs-4 col-md-4">
      {form_input('iv_account_nm' , set_value('iv_account_nm', {$info.iv_account_nm}) , 'class="form-control" placeholder="口座名義を入力してください"')}
      {if form_error('iv_account_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_account_nm')}</font></label>{/if}
    </div>
  </div>
*}

  <div class="form-group">
    <label for="iv_tag" class="col-md-3 control-label">タグ設定</label>
    <div class="col-md-8">
      {form_input('iv_tag' , set_value('iv_tag', {$info.iv_tag}) , 'class="form-control" placeholder="タグを入力してください。max.50文字"')}
      {if form_error('iv_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_tag')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="iv_memo" class="col-sm-3 control-label">メ　　モ</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="iv_memo" name="iv_memo" placeholder="請求書には反映されません。max.1000文字">{$tmp_memo}</textarea>
      {if form_error('iv_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('iv_memo')}</font></label>{/if}
    </div>
  </div>

<hr>

  <table class="table table-hover table-bordered">
    <tbody>
      <tr class="active">
        <td class="col-md-7 text-center">請　求　項　目　（下段：対象URL）</td>
        <td class="col-md-1 text-center">数 量</td>
        <td class="col-md-1 text-center">単 価（円）</td>
        <td class="col-md-1 text-center">金 額（円）</td>
      </tr>

      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input('ivd_item0' , set_value('ivd_item0','') , 'class="form-control" placeholder="キーワード文字を入力してください。"')}
          {if form_error('ivd_item0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_item0')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_qty0' , set_value('ivd_qty0', 0) , 'class="form-control text-center"')}
          {if form_error('ivd_qty0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_qty0')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_price0' , set_value('ivd_price0', 0) , 'class="form-control text-right"')}
          {if form_error('ivd_price0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_price0')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_total0' , set_value('ivd_total0', 0) , 'class="form-control text-right"')}
          {if form_error('ivd_total0')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_total0')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input("ivd_item_url0" , set_value("ivd_item_url0", '') , 'class="form-control" placeholder="対象URLを入力してください。"')}
          {if form_error("ivd_item_url0")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("ivd_item_url0")}</font></label>{/if}
        </td>
        <td colspan="3" class="col-md-1"></td>
      </tr>
      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input('ivd_item1' , set_value('ivd_item1','') , 'class="form-control" placeholder="キーワード文字を入力してください。"')}
          {if form_error('ivd_item1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_item1')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_qty1' , set_value('ivd_qty1', 0) , 'class="form-control text-center"')}
          {if form_error('ivd_qty1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_qty1')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_price1' , set_value('ivd_price1', 0) , 'class="form-control text-right"')}
          {if form_error('ivd_price1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_price1')}</font></label>{/if}
        </td>
        <td class="col-md-1 input-group-sm">
          {form_input('ivd_total1' , set_value('ivd_total1', 0) , 'class="form-control text-right"')}
          {if form_error('ivd_total1')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ivd_total1')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-7 input-group-sm">
          {form_input("ivd_item_url1" , set_value("ivd_item_url1", '') , 'class="form-control" placeholder="対象URLを入力してください。"')}
          {if form_error("ivd_item_url1")}<span class="label label-danger">Error : </span><label><font color=red>{form_error("ivd_item_url1")}</font></label>{/if}
        </td>
        <td colspan="3" class="col-md-1"></td>
      </tr>

    </tbody>
  </table>

  {form_hidden('iv_cm_seq',  $info.iv_cm_seq)}
  {form_hidden('iv_company_cm', $info.iv_company_cm)}
  {form_hidden('iv_salesman', $info.iv_salesman)}


  <br>
  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-3 col-sm-offset-3">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">登　　録</button>
    </div>
  </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">請求書情報　登録</h4>
        </div>
        <div class="modal-body">
          <p>登録しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
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
