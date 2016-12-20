{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

  <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　顧客情報　更新</p></H3>

{form_open('/customerlist/detailchk/' , 'name="customerDetailForm" class="form-horizontal h-adr"')}

  {$mess}

  {*if $info.cm_status!=2*}
  <div class="form-group">
    <label for="cm_status" class="col-xs-3 col-md-3 control-label">ステータス選択</label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('cm_status', $options_cm_status, set_value('cm_status', $info.cm_status))}
    </div>
  </div>
  {*/if*}
  <div class="form-group">
    <label for="cm_salesman" class="col-xs-3 col-md-3 control-label">担当営業</label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('cm_salesman', $options_cm_salesman, set_value('cm_salesman', $info.cm_salesman))}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_yayoi_cd" class="col-xs-3 col-md-3 control-label">顧客コード<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_yayoi_cd' , set_value('cm_yayoi_cd', $info.cm_yayoi_cd) , 'class="form-control" placeholder="顧客コードを入力"')}
      {if form_error('cm_yayoi_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_yayoi_cd')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_yayoi_name" class="col-md-3 control-label">弥生名称<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cm_yayoi_name' , set_value('cm_yayoi_name', $info.cm_yayoi_name) , 'class="form-control" placeholder="弥生名称を入力してください。max.50文字"')}
      {if form_error('cm_yayoi_name')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_yayoi_name')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_company" class="col-xs-3 col-md-3 control-label">会社名<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cm_company' , set_value('cm_company', $info.cm_company) , 'class="form-control" placeholder="会社名を入力してください"')}
      {if form_error('cm_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_company')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_company_kana" class="col-md-3 control-label">会社名カナ</label>
    <div class="col-md-8">
      {form_input('cm_company_kana' , set_value('cm_company_kana', $info.cm_company_kana) , 'class="form-control" placeholder="会社名カナを入力してください。max.50文字"')}
      {if form_error('cm_company_kana')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_company_kana')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_zip" class="col-xs-3 col-md-3 control-label">郵便番号<font color=red> *</font></label>
    <div class="col-xs-3 col-md-1">
      <span class="p-country-name" style="display:none;">Japan</span>
      {form_input('cm_zip01' , set_value('cm_zip01', $info.cm_zip01) , 'class="form-control p-postal-code" placeholder="郵便番号（3ケタ）"')}
      {if form_error('cm_zip01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_zip01')}</font></label>{/if}
    </div>
    <div class="col-xs-3 col-md-1">
      {form_input('cm_zip02' , set_value('cm_zip02', $info.cm_zip02) , 'class="form-control p-postal-code" placeholder="郵便番号（4ケタ）"')}
      {if form_error('cm_zip02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_zip02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_pref" class="col-xs-3 col-md-3 control-label">都道府県<font color=red> *</font></label>
    <div class="col-xs-3 col-md-2 btn-lg">
        <select name="cm_pref" class="p-region">
            <option value=""> -- 選択してください -- </option>
            <option value="北海道" {if $info.cm_pref=="北海道"}selected{/if}>北海道</option>
            <option value="青森県" {if $info.cm_pref=="青森県"}selected{/if}>青森県</option>
            <option value="岩手県" {if $info.cm_pref=="岩手県"}selected{/if}>岩手県</option>
            <option value="宮城県" {if $info.cm_pref=="宮城県"}selected{/if}>宮城県</option>
            <option value="秋田県" {if $info.cm_pref=="秋田県"}selected{/if}>秋田県</option>
            <option value="山形県" {if $info.cm_pref=="山形県"}selected{/if}>山形県</option>
            <option value="福島県" {if $info.cm_pref=="福島県"}selected{/if}>福島県</option>
            <option value="茨城県" {if $info.cm_pref=="茨城県"}selected{/if}>茨城県</option>
            <option value="栃木県" {if $info.cm_pref=="栃木県"}selected{/if}>栃木県</option>
            <option value="群馬県" {if $info.cm_pref=="群馬県"}selected{/if}>群馬県</option>
            <option value="埼玉県" {if $info.cm_pref=="埼玉県"}selected{/if}>埼玉県</option>
            <option value="千葉県" {if $info.cm_pref=="千葉県"}selected{/if}>千葉県</option>
            <option value="東京都" {if $info.cm_pref=="東京都"}selected{/if}>東京都</option>
            <option value="神奈川県" {if $info.cm_pref=="神奈川県"}selected{/if}>神奈川県</option>
            <option value="新潟県" {if $info.cm_pref=="新潟県"}selected{/if}>新潟県</option>
            <option value="富山県" {if $info.cm_pref=="富山県"}selected{/if}>富山県</option>
            <option value="石川県" {if $info.cm_pref=="石川県"}selected{/if}>石川県</option>
            <option value="福井県" {if $info.cm_pref=="福井県"}selected{/if}>福井県</option>
            <option value="山梨県" {if $info.cm_pref=="山梨県"}selected{/if}>山梨県</option>
            <option value="長野県" {if $info.cm_pref=="長野県"}selected{/if}>長野県</option>
            <option value="岐阜県" {if $info.cm_pref=="岐阜県"}selected{/if}>岐阜県</option>
            <option value="静岡県" {if $info.cm_pref=="静岡県"}selected{/if}>静岡県</option>
            <option value="愛知県" {if $info.cm_pref=="愛知県"}selected{/if}>愛知県</option>
            <option value="三重県" {if $info.cm_pref=="三重県"}selected{/if}>三重県</option>
            <option value="滋賀県" {if $info.cm_pref=="滋賀県"}selected{/if}>滋賀県</option>
            <option value="京都府" {if $info.cm_pref=="京都府"}selected{/if}>京都府</option>
            <option value="大阪府" {if $info.cm_pref=="大阪府"}selected{/if}>大阪府</option>
            <option value="兵庫県" {if $info.cm_pref=="兵庫県"}selected{/if}>兵庫県</option>
            <option value="奈良県" {if $info.cm_pref=="奈良県"}selected{/if}>奈良県</option>
            <option value="和歌山県" {if $info.cm_pref=="和歌山県"}selected{/if}>和歌山県</option>
            <option value="鳥取県" {if $info.cm_pref=="鳥取県"}selected{/if}>鳥取県</option>
            <option value="島根県" {if $info.cm_pref=="島根県"}selected{/if}>島根県</option>
            <option value="岡山県" {if $info.cm_pref=="岡山県"}selected{/if}>岡山県</option>
            <option value="広島県" {if $info.cm_pref=="広島県"}selected{/if}>広島県</option>
            <option value="山口県" {if $info.cm_pref=="山口県"}selected{/if}>山口県</option>
            <option value="徳島県" {if $info.cm_pref=="徳島県"}selected{/if}>徳島県</option>
            <option value="香川県" {if $info.cm_pref=="香川県"}selected{/if}>香川県</option>
            <option value="愛媛県" {if $info.cm_pref=="愛媛県"}selected{/if}>愛媛県</option>
            <option value="高知県" {if $info.cm_pref=="高知県"}selected{/if}>高知県</option>
            <option value="福岡県" {if $info.cm_pref=="福岡県"}selected{/if}>福岡県</option>
            <option value="佐賀県" {if $info.cm_pref=="佐賀県"}selected{/if}>佐賀県</option>
            <option value="長崎県" {if $info.cm_pref=="長崎県"}selected{/if}>長崎県</option>
            <option value="熊本県" {if $info.cm_pref=="熊本県"}selected{/if}>熊本県</option>
            <option value="大分県" {if $info.cm_pref=="大分県"}selected{/if}>大分県</option>
            <option value="宮崎県" {if $info.cm_pref=="宮崎県"}selected{/if}>宮崎県</option>
            <option value="鹿児島県" {if $info.cm_pref=="鹿児島県"}selected{/if}>鹿児島県</option>
            <option value="沖縄県" {if $info.cm_pref=="沖縄県"}selected{/if}>沖縄県</option>
        </select>
      {if form_error('cm_pref')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_pref')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_addr01" class="col-md-3 control-label">市区町村<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cm_addr01' , set_value('cm_addr01', $info.cm_addr01) , 'class="form-control p-locality" placeholder="市区町村を入力してください。 max.100文字"')}
      {if form_error('cm_addr01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_addr01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_addr02" class="col-md-3 control-label">町名・番地<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cm_addr02' , set_value('cm_addr02', $info.cm_addr02) , 'class="form-control p-street-address" placeholder="町名・番地を入力してください。 max.100文字"')}
      {if form_error('cm_addr02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_addr02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_buil" class="col-md-3 control-label">ビル・マンション名など</label>
    <div class="col-md-8">
      {form_input('cm_buil' , set_value('cm_buil', $info.cm_buil) , 'class="form-control p-extended-address" placeholder="ビル・マンション名などを入力してください。 max.100文字"')}
      {if form_error('cm_buil')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_buil')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_president" class="col-md-3 control-label">代表者<font color=red> *</font></label>
    <div class="col-md-4">
      {form_input('cm_president01' , set_value('cm_president01', $info.cm_president01) , 'class="form-control" placeholder="代表者姓を入力してください"')}
      {if form_error('cm_president01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_president01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('cm_president02' , set_value('cm_president02', $info.cm_president02) , 'class="form-control" placeholder="代表者名を入力してください"')}
      {if form_error('cm_president02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_president02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_tel01" class="col-xs-3 col-md-3 control-label">代表電話番号<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_tel01' , set_value('cm_tel01', $info.cm_tel01) , 'class="form-control" placeholder="代表電話番号を入力"')}
      {if form_error('cm_tel01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_tel01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_seturitu" class="col-xs-3 col-md-3 control-label">会社情報：設立年月日</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_seturitu' , set_value('cm_seturitu', $info.cm_seturitu) , 'class="form-control" placeholder="設立年月日を入力"')}
      {if form_error('cm_seturitu')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_seturitu')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_capital" class="col-xs-3 col-md-3 control-label">会社情報：資本金</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_capital' , set_value('cm_capital', $info.cm_capital) , 'class="form-control" placeholder="設立年月日を入力"')}
      {if form_error('cm_capital')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_capital')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_closingdate" class="col-xs-3 col-md-3 control-label">会社情報：決算日</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_closingdate' , set_value('cm_closingdate', $info.cm_closingdate) , 'class="form-control" placeholder="決算日を入力"')}
      {if form_error('cm_closingdate')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_closingdate')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_employee" class="col-xs-3 col-md-3 control-label">会社情報：従業員数</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_employee' , set_value('cm_employee', $info.cm_employee) , 'class="form-control" placeholder="従業員数を入力"')}
      {if form_error('cm_employee')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_employee')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_collect" class="col-sm-3 control-label">回収サイト</label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('cm_collect', $options_cm_collect, set_value('cm_collect', $info.cm_collect))}
      {if form_error('cm_collect')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_collect')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_credit_chk" class="col-xs-3 col-md-3 control-label">与信チェック日</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_credit_chk' , set_value('cm_credit_chk', $info.cm_credit_chk) , 'class="form-control" placeholder="与信チェック日を入力"')}
      {if form_error('cm_credit_chk')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_credit_chk')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_antisocial_chk" class="col-xs-3 col-md-3 control-label">反社チェック日</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_antisocial_chk' , set_value('cm_antisocial_chk', $info.cm_antisocial_chk) , 'class="form-control" placeholder="反社チェック日を入力"')}
      {if form_error('cm_antisocial_chk')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_antisocial_chk')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_credit_max" class="col-xs-3 col-md-3 control-label">与信限度額</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_credit_max' , set_value('cm_credit_max', $info.cm_credit_max) , 'class="form-control" placeholder="与信限度額を入力"')}
      {if form_error('cm_credit_max')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_credit_max')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_trade_no" class="col-xs-3 col-md-3 control-label">取引申請番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_trade_no' , set_value('cm_trade_no', $info.cm_trade_no) , 'class="form-control" placeholder="取引申請番号を入力"')}
      {if form_error('cm_trade_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_trade_no')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_mail" class="col-md-3 control-label">メールアドレス<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cm_mail' , set_value('cm_mail', $info.cm_mail) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください"')}
      {if form_error('cm_mail')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_mail')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_mailsub" class="col-md-3 control-label">メールアドレス(サブ)</label>
    <div class="col-md-8">
      {form_input('cm_mailsub' , set_value('cm_mailsub', $info.cm_mailsub) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください"')}
      {if form_error('cm_mailsub')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_mailsub')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
  <label for="cm_department" class="col-md-3 control-label">担当所属部署</label>
    <div class="col-md-8">
      {form_input('cm_department' , set_value('cm_department', $info.cm_department) , 'class="form-control" placeholder="所属部署を入力してください"')}
      {if form_error('cm_department')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_department')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_person" class="col-md-3 control-label">担当者<font color=red> *</font></label>
    <div class="col-md-4">
      {form_input('cm_person01' , set_value('cm_person01', $info.cm_person01) , 'class="form-control" placeholder="担当者姓を入力してください"')}
      {if form_error('cm_person01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_person01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('cm_person02' , set_value('cm_person02', $info.cm_person02) , 'class="form-control" placeholder="担当者名を入力してください"')}
      {if form_error('cm_person02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_person02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_tel02" class="col-xs-3 col-md-3 control-label">担当者電話番号<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_tel02' , set_value('cm_tel02', $info.cm_tel02) , 'class="form-control" placeholder="担当者電話番号を入力"')}
      {if form_error('cm_tel02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_tel02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_mobile" class="col-xs-3 col-md-3 control-label">担当者携帯番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_mobile' , set_value('cm_mobile', $info.cm_mobile) , 'class="form-control" placeholder="担当者携帯番号を入力"')}
      {if form_error('cm_mobile')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_mobile')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_fax" class="col-xs-3 col-md-3 control-label">FAX番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_fax' , set_value('cm_fax', $info.cm_fax) , 'class="form-control" placeholder="FAX番号を入力"')}
      {if form_error('cm_fax')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_fax')}</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="cm_bank_cd" class="col-xs-3 col-md-3 control-label">銀行CD<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_bank_cd' , set_value('cm_bank_cd', $info.cm_bank_cd) , 'class="form-control" placeholder="銀行CDを入力"')}
      {if form_error('cm_bank_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_bank_cd')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_bank_nm" class="col-xs-3 col-md-3 control-label">銀行名<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
      {form_input('cm_bank_nm' , set_value('cm_bank_nm', $info.cm_bank_nm) , 'class="form-control" placeholder="銀行名を入力してください"')}
      {if form_error('cm_bank_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_bank_nm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_branch_cd" class="col-xs-3 col-md-3 control-label">支店CD<font color=red> *</font></label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_branch_cd' , set_value('cm_branch_cd', $info.cm_branch_cd) , 'class="form-control" placeholder="支店CDを入力"')}
      {if form_error('cm_branch_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_branch_cd')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_branch_nm" class="col-xs-3 col-md-3 control-label">支店名<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
      {form_input('cm_branch_nm' , set_value('cm_branch_nm', $info.cm_branch_nm) , 'class="form-control" placeholder="支店名を入力してください"')}
      {if form_error('cm_branch_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_branch_nm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_kind" class="col-sm-3 control-label">口座種別選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('cm_kind', $options_cm_kind, set_value('cm_kind', $info.cm_kind))}
      {if form_error('cm_kind')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_kind')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_account_no" class="col-xs-3 col-md-3 control-label">口座番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('cm_account_no' , set_value('cm_account_no', $info.cm_account_no) , 'class="form-control" placeholder="口座番号を入力"')}
      {if form_error('cm_account_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_account_no')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_account_nm" class="col-xs-3 col-md-3 control-label">口座名義(半角カナ)<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
      {form_input('cm_account_nm' , set_value('cm_account_nm', $info.cm_account_nm) , 'class="form-control" placeholder="口座名義(半角カナ)を入力してください"')}
      {if form_error('cm_account_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_account_nm')}</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="cm_memo" class="col-sm-3 control-label">備　　考</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="cm_memo" name="cm_memo" placeholder="max.1000文字">{$info.cm_memo}</textarea>
      {if form_error('cm_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_memo')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_memo_iv" class="col-sm-3 control-label">請求書：備考</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="cm_memo_iv" name="cm_memo_iv" placeholder="max.100文字">{$info.cm_memo_iv}</textarea>
      {if form_error('cm_memo_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_memo_iv')}</font></label>{/if}
    </div>
  </div>

  <hr>

  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-8">
      {form_checkbox('chkinvoice[]','1',"{if $info.cm_flg_iv==1}1{else}0{/if}")}請求書の送付先が異なる場合にはチェックを入れて下記に記入してください。
    </div>
  </div>
  <div class="form-group">
    <label for="cm_company_iv" class="col-md-3 control-label">請求書：会社名</label>
    <div class="col-md-8">
      {form_input('cm_company_iv' , set_value('cm_company_iv', $info.cm_company_iv) , 'class="form-control" placeholder="会社名を入力してください。max.50文字"')}
      {if form_error('cm_company_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_company_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_zip_iv" class="col-xs-3 col-md-3 control-label">請求書：郵便番号</label>
    <div class="col-xs-3 col-md-2">
      {form_input('cm_zip01_iv' , set_value('cm_zip01_iv', $info.cm_zip01_iv) , 'class="form-control" placeholder="郵便番号（3ケタ）"')}
      {if form_error('cm_zip01_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_zip01_iv')}</font></label>{/if}
    </div>
    <div class="col-xs-3 col-md-2">
      {form_input('cm_zip02_iv' , set_value('cm_zip02_iv', $info.cm_zip02_iv) , 'class="form-control" placeholder="郵便番号（4ケタ）"')}
      {if form_error('cm_zip02_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_zip02_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_pref_iv" class="col-xs-3 col-md-3 control-label">請求書：都道府県</label>
    <div class="col-xs-3 col-md-2 btn-lg">
        <select name="cm_pref_iv">
            <option value=""> -- 選択してください -- </option>
            <option value="北海道" {if $info.cm_pref_iv=="北海道"}selected{/if}>北海道</option>
            <option value="青森県" {if $info.cm_pref_iv=="青森県"}selected{/if}>青森県</option>
            <option value="岩手県" {if $info.cm_pref_iv=="岩手県"}selected{/if}>岩手県</option>
            <option value="宮城県" {if $info.cm_pref_iv=="宮城県"}selected{/if}>宮城県</option>
            <option value="秋田県" {if $info.cm_pref_iv=="秋田県"}selected{/if}>秋田県</option>
            <option value="山形県" {if $info.cm_pref_iv=="山形県"}selected{/if}>山形県</option>
            <option value="福島県" {if $info.cm_pref_iv=="福島県"}selected{/if}>福島県</option>
            <option value="茨城県" {if $info.cm_pref_iv=="茨城県"}selected{/if}>茨城県</option>
            <option value="栃木県" {if $info.cm_pref_iv=="栃木県"}selected{/if}>栃木県</option>
            <option value="群馬県" {if $info.cm_pref_iv=="群馬県"}selected{/if}>群馬県</option>
            <option value="埼玉県" {if $info.cm_pref_iv=="埼玉県"}selected{/if}>埼玉県</option>
            <option value="千葉県" {if $info.cm_pref_iv=="千葉県"}selected{/if}>千葉県</option>
            <option value="東京都" {if $info.cm_pref_iv=="東京都"}selected{/if}>東京都</option>
            <option value="神奈川県" {if $info.cm_pref_iv=="神奈川県"}selected{/if}>神奈川県</option>
            <option value="新潟県" {if $info.cm_pref_iv=="新潟県"}selected{/if}>新潟県</option>
            <option value="富山県" {if $info.cm_pref_iv=="富山県"}selected{/if}>富山県</option>
            <option value="石川県" {if $info.cm_pref_iv=="石川県"}selected{/if}>石川県</option>
            <option value="福井県" {if $info.cm_pref_iv=="福井県"}selected{/if}>福井県</option>
            <option value="山梨県" {if $info.cm_pref_iv=="山梨県"}selected{/if}>山梨県</option>
            <option value="長野県" {if $info.cm_pref_iv=="長野県"}selected{/if}>長野県</option>
            <option value="岐阜県" {if $info.cm_pref_iv=="岐阜県"}selected{/if}>岐阜県</option>
            <option value="静岡県" {if $info.cm_pref_iv=="静岡県"}selected{/if}>静岡県</option>
            <option value="愛知県" {if $info.cm_pref_iv=="愛知県"}selected{/if}>愛知県</option>
            <option value="三重県" {if $info.cm_pref_iv=="三重県"}selected{/if}>三重県</option>
            <option value="滋賀県" {if $info.cm_pref_iv=="滋賀県"}selected{/if}>滋賀県</option>
            <option value="京都府" {if $info.cm_pref_iv=="京都府"}selected{/if}>京都府</option>
            <option value="大阪府" {if $info.cm_pref_iv=="大阪府"}selected{/if}>大阪府</option>
            <option value="兵庫県" {if $info.cm_pref_iv=="兵庫県"}selected{/if}>兵庫県</option>
            <option value="奈良県" {if $info.cm_pref_iv=="奈良県"}selected{/if}>奈良県</option>
            <option value="和歌山県" {if $info.cm_pref_iv=="和歌山県"}selected{/if}>和歌山県</option>
            <option value="鳥取県" {if $info.cm_pref_iv=="鳥取県"}selected{/if}>鳥取県</option>
            <option value="島根県" {if $info.cm_pref_iv=="島根県"}selected{/if}>島根県</option>
            <option value="岡山県" {if $info.cm_pref_iv=="岡山県"}selected{/if}>岡山県</option>
            <option value="広島県" {if $info.cm_pref_iv=="広島県"}selected{/if}>広島県</option>
            <option value="山口県" {if $info.cm_pref_iv=="山口県"}selected{/if}>山口県</option>
            <option value="徳島県" {if $info.cm_pref_iv=="徳島県"}selected{/if}>徳島県</option>
            <option value="香川県" {if $info.cm_pref_iv=="香川県"}selected{/if}>香川県</option>
            <option value="愛媛県" {if $info.cm_pref_iv=="愛媛県"}selected{/if}>愛媛県</option>
            <option value="高知県" {if $info.cm_pref_iv=="高知県"}selected{/if}>高知県</option>
            <option value="福岡県" {if $info.cm_pref_iv=="福岡県"}selected{/if}>福岡県</option>
            <option value="佐賀県" {if $info.cm_pref_iv=="佐賀県"}selected{/if}>佐賀県</option>
            <option value="長崎県" {if $info.cm_pref_iv=="長崎県"}selected{/if}>長崎県</option>
            <option value="熊本県" {if $info.cm_pref_iv=="熊本県"}selected{/if}>熊本県</option>
            <option value="大分県" {if $info.cm_pref_iv=="大分県"}selected{/if}>大分県</option>
            <option value="宮崎県" {if $info.cm_pref_iv=="宮崎県"}selected{/if}>宮崎県</option>
            <option value="鹿児島県" {if $info.cm_pref_iv=="鹿児島県"}selected{/if}>鹿児島県</option>
            <option value="沖縄県" {if $info.cm_pref_iv=="沖縄県"}selected{/if}>沖縄県</option>
        </select>
      {if form_error('cm_pref_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_pref_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_addr01_iv" class="col-md-3 control-label">請求書：市区町村</label>
    <div class="col-md-8">
      {form_input('cm_addr01_iv' , set_value('cm_addr01_iv', $info.cm_addr01_iv) , 'class="form-control" placeholder="市区町村を入力してください。 max.100文字"')}
      {if form_error('cm_addr01_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_addr01_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_addr02_iv" class="col-md-3 control-label">請求書：町名・番地</label>
    <div class="col-md-8">
      {form_input('cm_addr02_iv' , set_value('cm_addr02_iv', $info.cm_addr02_iv) , 'class="form-control" placeholder="町名・番地を入力してください。 max.100文字"')}
      {if form_error('cm_addr02_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_addr02_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_buil_iv" class="col-md-3 control-label">請求書：ビル・マンション名など</label>
    <div class="col-md-8">
      {form_input('cm_buil_iv' , set_value('cm_buil_iv', $info.cm_buil_iv) , 'class="form-control p-extended-address" placeholder="ビル・マンション名などを入力してください。 max.100文字"')}
      {if form_error('cm_buil_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_buil_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_department_iv" class="col-md-3 control-label">請求書：担当所属部署</label>
    <div class="col-md-8">
      {form_input('cm_department_iv' , set_value('cm_department_iv', $info.cm_department_iv) , 'class="form-control" placeholder="所属部署を入力してください。max.50文字"')}
      {if form_error('cm_department_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_department_iv')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cm_person_iv" class="col-md-3 control-label">請求書：担当者</label>
    <div class="col-md-4">
      {form_input('cm_person01_iv' , set_value('cm_person01_iv', $info.cm_person01_iv) , 'class="form-control" placeholder="担当者姓を入力してください。max.50文字"')}
      {if form_error('cm_person01_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_person01_iv')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('cm_person02_iv' , set_value('cm_person02_iv', $info.cm_person02_iv) , 'class="form-control" placeholder="担当者名を入力してください。max.50文字"')}
      {if form_error('cm_person02_iv')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cm_person02_iv')}</font></label>{/if}
    </div>
  </div>



  {form_hidden('cm_seq', $info.cm_seq)}

  <!-- Button trigger modal -->
  {*if $info.cm_status!=2*}
  <div class="row">
  <div class="col-sm-2 col-sm-offset-3">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>
  </div>
  </div>
  {*/if*}

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">顧客情報　更新</h4>
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
