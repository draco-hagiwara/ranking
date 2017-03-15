{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

  <script src="https://{$smarty.server.SERVER_NAME}/js/repeater/jquery-1.11.1.js"></script>
  <script src="https://{$smarty.server.SERVER_NAME}/js/repeater/jquery.repeater.min.js"></script>

  <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　支払情報　更新</p></H3>

{form_open('/shokailist/detailchk/' , 'name="detailForm" class="form-horizontal h-adr repeater"')}

  {$mess}

  <div class="form-group">
    <label for="sk_status" class="col-xs-3 col-md-3 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-xs-9 col-md-9 btn-lg">
      {form_dropdown('sk_status', $options_sk_status, set_value('sk_status', $info.sk_status))}
      {if form_error('sk_status')}<br><span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_status')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_salesman" class="col-xs-3 col-md-3 control-label">担当営業<font color=red> *</font></label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('sk_salesman', $options_sk_salesman, set_value('sk_salesman', $info.sk_salesman))}
    </div>
  </div>
  <div class="form-group">
    <label for="skc_cm_seq" class="col-md-3 control-label">売上先会社 選択<font color=red> *</font><br>(最大登録数：9社)</label>
    <div class="col-md-9 btn-lg">
      <div data-repeater-list="group-cm">
        {foreach from=$info_comp item=comp}
        <div data-repeater-item>
          {form_dropdown('skc_cm_seq', $options_cm_company, set_value('skc_cm_seq', $comp.skc_cm_seq))}
          <input data-repeater-delete type="button" value="削除"/>
        </div>
        {/foreach}
      </div>
      <input data-repeater-create type="button" value="入力フォームを追加"/>
    </div>
  </div>
  <div class="form-group">
    <label for="sk_tax_out" class="col-md-3 control-label">消費税有無の指定<font color=red> *</font></label>
    <div class="col-md-8">
      <label class="radio-inline">
        <input type="radio" name="sk_tax_out" id="inlineRadio1" value="1" {if $info.sk_tax_out==1}checked{/if}> 内税
      </label>
      <label class="radio-inline">
        <input type="radio" name="sk_tax_out" id="inlineRadio2" value="0" {if $info.sk_tax_out==0}checked{/if}> 外税
      </label>
    </div>
  </div>
  {*<div class="form-group">
    <label for="sk_paycal" class="col-xs-3 col-md-3 control-label">紹介料計算式<font color=red> *</font></label>
    <div class="col-xs-3 col-md-2">
      固定金額：{form_input('sk_paycal_fix' , set_value('sk_paycal_fix', $info.sk_paycal_fix) , 'class="form-control" placeholder="固定金額"')}
      {if form_error('sk_paycal_fix')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_paycal_fix')}</font></label>{/if}
    </div>
    <div class="col-xs-3 col-md-2">
      料率：{form_input('sk_paycal_rate' , set_value('sk_paycal_rate', $info.sk_paycal_rate) , 'class="form-control" placeholder="料率"')}
      {if form_error('sk_paycal_rate')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_paycal_rate')}</font></label>{/if}
    </div>
    <div class="col-xs-8 col-md-4">
      ⇒⇒　<u><font color=blue>固定金額</font>　+（<font color=blue> 料率</font>　×　売上高 ）</u>
    </div>
  </div>
  *}
  <div class="form-group">
    <label for="sk_payment" class="col-sm-3 control-label">支払サイト<font color=red> *</font></label>
    <div class="col-sm-9 btn-lg">
      {form_dropdown('sk_payment', $options_sk_payment, set_value('sk_payment', $info.sk_payment))}
      {if form_error('sk_payment')}<br><span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_payment')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_company" class="col-xs-3 col-md-3 control-label">会社名<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('sk_company' , set_value('sk_company', $info.sk_company) , 'class="form-control" placeholder="会社名を入力してください"')}
      {if form_error('sk_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_company')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_company_kana" class="col-xs-3 col-md-3 control-label">会社名カナ<font color=red> *</font></label>
    <div class="col-md-2">
      {form_input('sk_company_kana' , set_value('sk_company_kana', $info.sk_company_kana) , 'class="form-control" placeholder="全角カナ。max.4文字"')}
      {if form_error('sk_company_kana')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_company_kana')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_zip" class="col-xs-3 col-md-3 control-label">郵便番号</label>
    <div class="col-xs-3 col-md-1">
      <span class="p-country-name" style="display:none;">Japan</span>
      {form_input('sk_zip01' , set_value('sk_zip01', $info.sk_zip01) , 'class="form-control p-postal-code" placeholder="郵便番号（3ケタ）"')}
      {if form_error('sk_zip01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_zip01')}</font></label>{/if}
    </div>
    <div class="col-xs-3 col-md-1">
      {form_input('sk_zip02' , set_value('sk_zip02', $info.sk_zip02) , 'class="form-control p-postal-code" placeholder="郵便番号（4ケタ）"')}
      {if form_error('sk_zip02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_zip02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_pref" class="col-xs-3 col-md-3 control-label">都道府県</label>
    <div class="col-xs-3 col-md-2 btn-lg">
        <select name="sk_pref" class="p-region">
            <option value=""> -- 選択してください -- </option>
            <option value="北海道" {if $info.sk_pref=="北海道"}selected{/if}>北海道</option>
            <option value="青森県" {if $info.sk_pref=="青森県"}selected{/if}>青森県</option>
            <option value="岩手県" {if $info.sk_pref=="岩手県"}selected{/if}>岩手県</option>
            <option value="宮城県" {if $info.sk_pref=="宮城県"}selected{/if}>宮城県</option>
            <option value="秋田県" {if $info.sk_pref=="秋田県"}selected{/if}>秋田県</option>
            <option value="山形県" {if $info.sk_pref=="山形県"}selected{/if}>山形県</option>
            <option value="福島県" {if $info.sk_pref=="福島県"}selected{/if}>福島県</option>
            <option value="茨城県" {if $info.sk_pref=="茨城県"}selected{/if}>茨城県</option>
            <option value="栃木県" {if $info.sk_pref=="栃木県"}selected{/if}>栃木県</option>
            <option value="群馬県" {if $info.sk_pref=="群馬県"}selected{/if}>群馬県</option>
            <option value="埼玉県" {if $info.sk_pref=="埼玉県"}selected{/if}>埼玉県</option>
            <option value="千葉県" {if $info.sk_pref=="千葉県"}selected{/if}>千葉県</option>
            <option value="東京都" {if $info.sk_pref=="東京都"}selected{/if}>東京都</option>
            <option value="神奈川県" {if $info.sk_pref=="神奈川県"}selected{/if}>神奈川県</option>
            <option value="新潟県" {if $info.sk_pref=="新潟県"}selected{/if}>新潟県</option>
            <option value="富山県" {if $info.sk_pref=="富山県"}selected{/if}>富山県</option>
            <option value="石川県" {if $info.sk_pref=="石川県"}selected{/if}>石川県</option>
            <option value="福井県" {if $info.sk_pref=="福井県"}selected{/if}>福井県</option>
            <option value="山梨県" {if $info.sk_pref=="山梨県"}selected{/if}>山梨県</option>
            <option value="長野県" {if $info.sk_pref=="長野県"}selected{/if}>長野県</option>
            <option value="岐阜県" {if $info.sk_pref=="岐阜県"}selected{/if}>岐阜県</option>
            <option value="静岡県" {if $info.sk_pref=="静岡県"}selected{/if}>静岡県</option>
            <option value="愛知県" {if $info.sk_pref=="愛知県"}selected{/if}>愛知県</option>
            <option value="三重県" {if $info.sk_pref=="三重県"}selected{/if}>三重県</option>
            <option value="滋賀県" {if $info.sk_pref=="滋賀県"}selected{/if}>滋賀県</option>
            <option value="京都府" {if $info.sk_pref=="京都府"}selected{/if}>京都府</option>
            <option value="大阪府" {if $info.sk_pref=="大阪府"}selected{/if}>大阪府</option>
            <option value="兵庫県" {if $info.sk_pref=="兵庫県"}selected{/if}>兵庫県</option>
            <option value="奈良県" {if $info.sk_pref=="奈良県"}selected{/if}>奈良県</option>
            <option value="和歌山県" {if $info.sk_pref=="和歌山県"}selected{/if}>和歌山県</option>
            <option value="鳥取県" {if $info.sk_pref=="鳥取県"}selected{/if}>鳥取県</option>
            <option value="島根県" {if $info.sk_pref=="島根県"}selected{/if}>島根県</option>
            <option value="岡山県" {if $info.sk_pref=="岡山県"}selected{/if}>岡山県</option>
            <option value="広島県" {if $info.sk_pref=="広島県"}selected{/if}>広島県</option>
            <option value="山口県" {if $info.sk_pref=="山口県"}selected{/if}>山口県</option>
            <option value="徳島県" {if $info.sk_pref=="徳島県"}selected{/if}>徳島県</option>
            <option value="香川県" {if $info.sk_pref=="香川県"}selected{/if}>香川県</option>
            <option value="愛媛県" {if $info.sk_pref=="愛媛県"}selected{/if}>愛媛県</option>
            <option value="高知県" {if $info.sk_pref=="高知県"}selected{/if}>高知県</option>
            <option value="福岡県" {if $info.sk_pref=="福岡県"}selected{/if}>福岡県</option>
            <option value="佐賀県" {if $info.sk_pref=="佐賀県"}selected{/if}>佐賀県</option>
            <option value="長崎県" {if $info.sk_pref=="長崎県"}selected{/if}>長崎県</option>
            <option value="熊本県" {if $info.sk_pref=="熊本県"}selected{/if}>熊本県</option>
            <option value="大分県" {if $info.sk_pref=="大分県"}selected{/if}>大分県</option>
            <option value="宮崎県" {if $info.sk_pref=="宮崎県"}selected{/if}>宮崎県</option>
            <option value="鹿児島県" {if $info.sk_pref=="鹿児島県"}selected{/if}>鹿児島県</option>
            <option value="沖縄県" {if $info.sk_pref=="沖縄県"}selected{/if}>沖縄県</option>
        </select>
      {if form_error('sk_pref')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_pref')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_addr01" class="col-md-3 control-label">市区町村</label>
    <div class="col-md-8">
      {form_input('sk_addr01' , set_value('sk_addr01', $info.sk_addr01) , 'class="form-control p-locality" placeholder="市区町村を入力してください。 max.100文字"')}
      {if form_error('sk_addr01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_addr01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_addr02" class="col-md-3 control-label">町名・番地</label>
    <div class="col-md-8">
      {form_input('sk_addr02' , set_value('sk_addr02', $info.sk_addr02) , 'class="form-control p-street-address" placeholder="町名・番地を入力してください。 max.100文字"')}
      {if form_error('sk_addr02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_addr02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_buil" class="col-md-3 control-label">ビル・マンション名など</label>
    <div class="col-md-8">
      {form_input('sk_buil' , set_value('sk_buil', $info.sk_buil) , 'class="form-control p-extended-address" placeholder="ビル・マンション名などを入力してください。 max.100文字"')}
      {if form_error('sk_buil')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_buil')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_president" class="col-md-3 control-label">代表者</label>
    <div class="col-md-4">
      {form_input('sk_president01' , set_value('sk_president01', $info.sk_president01) , 'class="form-control" placeholder="代表者姓を入力してください"')}
      {if form_error('sk_president01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_president01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('sk_president02' , set_value('sk_president02', $info.sk_president02) , 'class="form-control" placeholder="代表者名を入力してください"')}
      {if form_error('sk_president02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_president02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_tel01" class="col-xs-3 col-md-3 control-label">代表電話番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_tel01' , set_value('sk_tel01', $info.sk_tel01) , 'class="form-control" placeholder="代表電話番号を入力"')}
      {if form_error('sk_tel01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_tel01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_mail" class="col-md-3 control-label">メールアドレス</label>
    <div class="col-md-8">
      {form_input('sk_mail' , set_value('sk_mail', $info.sk_mail) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください"')}
      {if form_error('sk_mail')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_mail')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_mailsub" class="col-md-3 control-label">メールアドレス(サブ)</label>
    <div class="col-md-8">
      {form_input('sk_mailsub' , set_value('sk_mailsub', $info.sk_mailsub) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください"')}
      {if form_error('sk_mailsub')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_mailsub')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
  <label for="sk_department" class="col-md-3 control-label">担当所属部署</label>
    <div class="col-md-8">
      {form_input('sk_department' , set_value('sk_department', $info.sk_department) , 'class="form-control" placeholder="所属部署を入力してください"')}
      {if form_error('sk_department')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_department')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_person" class="col-md-3 control-label">担当者</label>
    <div class="col-md-4">
      {form_input('sk_person01' , set_value('sk_person01', $info.sk_person01) , 'class="form-control" placeholder="担当者姓を入力してください"')}
      {if form_error('sk_person01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_person01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('sk_person02' , set_value('sk_person02', $info.sk_person02) , 'class="form-control" placeholder="担当者名を入力してください"')}
      {if form_error('sk_person02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_person02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_tel02" class="col-xs-3 col-md-3 control-label">担当者電話番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_tel02' , set_value('sk_tel02', $info.sk_tel02) , 'class="form-control" placeholder="担当者電話番号を入力"')}
      {if form_error('sk_tel02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_tel02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_mobile" class="col-xs-3 col-md-3 control-label">担当者携帯番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_mobile' , set_value('sk_mobile', $info.sk_mobile) , 'class="form-control" placeholder="担当者携帯番号を入力"')}
      {if form_error('sk_mobile')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_mobile')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_fax" class="col-xs-3 col-md-3 control-label">FAX番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_fax' , set_value('sk_fax', $info.sk_fax) , 'class="form-control" placeholder="FAX番号を入力"')}
      {if form_error('sk_fax')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_fax')}</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="sk_bank_cd" class="col-xs-3 col-md-3 control-label">銀行CD</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_bank_cd' , set_value('sk_bank_cd', $info.sk_bank_cd) , 'class="form-control" placeholder="銀行CDを入力"')}
      {if form_error('sk_bank_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_bank_cd')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_bank_nm" class="col-xs-3 col-md-3 control-label">銀行名</label>
    <div class="col-xs-4 col-md-4">
      {form_input('sk_bank_nm' , set_value('sk_bank_nm', $info.sk_bank_nm) , 'class="form-control" placeholder="銀行名を入力してください"')}
      {if form_error('sk_bank_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_bank_nm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_branch_cd" class="col-xs-3 col-md-3 control-label">支店CD</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_branch_cd' , set_value('sk_branch_cd', $info.sk_branch_cd) , 'class="form-control" placeholder="支店CDを入力"')}
      {if form_error('sk_branch_cd')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_branch_cd')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_branch_nm" class="col-xs-3 col-md-3 control-label">支店名</label>
    <div class="col-xs-4 col-md-4">
      {form_input('sk_branch_nm' , set_value('sk_branch_nm', $info.sk_branch_nm) , 'class="form-control" placeholder="支店名を入力してください"')}
      {if form_error('sk_branch_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_branch_nm')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_kind" class="col-sm-3 control-label">口座種別選択</label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('sk_kind', $options_sk_kind, set_value('sk_kind', $info.sk_kind))}
      {if form_error('sk_kind')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_kind')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_account_no" class="col-xs-3 col-md-3 control-label">口座番号</label>
    <div class="col-xs-2 col-md-2">
      {form_input('sk_account_no' , set_value('sk_account_no', $info.sk_account_no) , 'class="form-control" placeholder="口座番号を入力"')}
      {if form_error('sk_account_no')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_account_no')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="sk_account_nm" class="col-xs-3 col-md-3 control-label">口座名義(半角カナ)</label>
    <div class="col-xs-4 col-md-4">
      {form_input('sk_account_nm' , set_value('sk_account_nm', $info.sk_account_nm) , 'class="form-control" placeholder="口座名義(半角カナ)を入力してください"')}
      {if form_error('sk_account_nm')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_account_nm')}</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="sk_memo" class="col-sm-3 control-label">備　　考</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="sk_memo" name="sk_memo" placeholder="max.1000文字">{$info.sk_memo}</textarea>
      {if form_error('sk_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('sk_memo')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('sk_seq', $info.sk_seq)}

  <!-- Button trigger modal -->
  <div class="row">
  <div class="col-sm-2 col-sm-offset-3">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>
  </div>
  </div>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

<script>
$(document).ready(function () {
  'use strict';
  $('.repeater').repeater({
    defaultValues: {
      'skc_cm_seq': '売上先会社'
    },
    show: function () {
      $(this).slideDown();
    },
    hide: function (deleteElement) {
      if(confirm('削除してもいいですか？')) {
        $(this).slideUp(deleteElement);
      }
    }
  });
});
</script>

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
