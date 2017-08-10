{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

  <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　クライアント情報　更新</p></H3>

{form_open('/clientlist/detailchk/' , 'name="clientDetailForm" class="form-horizontal h-adr"')}

  {$mess}
  <div class="form-group">
    <label for="cl_status" class="col-xs-3 col-md-3 control-label">ステータス選択</label>
    <div class="col-xs-4 col-md-2 btn-lg">
      {form_dropdown('cl_status', $options_cl_status, set_value('cl_status', $info.cl_status))}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_contract" class="col-xs-3 col-md-3 control-label">契約期間</label>
    <div class="col-xs-4 col-md-2">
      {form_input('cl_contract_str' , set_value('cl_contract_str', $info.cl_contract_str) , 'class="form-control" placeholder="契約開始日(yyyy-dd-mm)を入力してください"')}
      {if form_error('cl_contract_str')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_contract_str')}</font></label>{/if}
    </div>
    <div class="col-xs-4 col-md-2">
      {form_input('cl_contract_end' , set_value('cl_contract_end', $info.cl_contract_end) , 'class="form-control" placeholder="契約終了日(yyyy-dd-mm)を入力してください"')}
      {if form_error('cl_contract_end')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_contract_end')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_company" class="col-xs-3 col-md-3 control-label">会社名<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cl_company' , set_value('cl_company', $info.cl_company) , 'class="form-control" placeholder="会社名を入力してください"')}
      {if form_error('cl_company')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_company')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_zip" class="col-xs-3 col-md-3 control-label">郵便番号<font color=red> *</font></label>
    <div class="col-xs-3 col-md-1">
      <span class="p-country-name" style="display:none;">Japan</span>
      {form_input('cl_zip01' , set_value('cl_zip01', $info.cl_zip01) , 'class="form-control p-postal-code" placeholder="郵便番号（3ケタ）"')}
      {if form_error('cl_zip01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_zip01')}</font></label>{/if}
    </div>
    <div class="col-xs-3 col-md-1">
      {form_input('cl_zip02' , set_value('cl_zip02', $info.cl_zip02) , 'class="form-control p-postal-code" placeholder="郵便番号（4ケタ）"')}
      {if form_error('cl_zip02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_zip02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_pref" class="col-xs-3 col-md-3 control-label">都道府県<font color=red> *</font></label>
    <div class="col-xs-3 col-md-2 btn-lg">
        <select name="cl_pref" class="p-region">
            <option value=""> -- 選択してください -- </option>
            <option value="北海道" {if $info.cl_pref=="北海道"}selected{/if}>北海道</option>
            <option value="青森県" {if $info.cl_pref=="青森県"}selected{/if}>青森県</option>
            <option value="岩手県" {if $info.cl_pref=="岩手県"}selected{/if}>岩手県</option>
            <option value="宮城県" {if $info.cl_pref=="宮城県"}selected{/if}>宮城県</option>
            <option value="秋田県" {if $info.cl_pref=="秋田県"}selected{/if}>秋田県</option>
            <option value="山形県" {if $info.cl_pref=="山形県"}selected{/if}>山形県</option>
            <option value="福島県" {if $info.cl_pref=="福島県"}selected{/if}>福島県</option>
            <option value="茨城県" {if $info.cl_pref=="茨城県"}selected{/if}>茨城県</option>
            <option value="栃木県" {if $info.cl_pref=="栃木県"}selected{/if}>栃木県</option>
            <option value="群馬県" {if $info.cl_pref=="群馬県"}selected{/if}>群馬県</option>
            <option value="埼玉県" {if $info.cl_pref=="埼玉県"}selected{/if}>埼玉県</option>
            <option value="千葉県" {if $info.cl_pref=="千葉県"}selected{/if}>千葉県</option>
            <option value="東京都" {if $info.cl_pref=="東京都"}selected{/if}>東京都</option>
            <option value="神奈川県" {if $info.cl_pref=="神奈川県"}selected{/if}>神奈川県</option>
            <option value="新潟県" {if $info.cl_pref=="新潟県"}selected{/if}>新潟県</option>
            <option value="富山県" {if $info.cl_pref=="富山県"}selected{/if}>富山県</option>
            <option value="石川県" {if $info.cl_pref=="石川県"}selected{/if}>石川県</option>
            <option value="福井県" {if $info.cl_pref=="福井県"}selected{/if}>福井県</option>
            <option value="山梨県" {if $info.cl_pref=="山梨県"}selected{/if}>山梨県</option>
            <option value="長野県" {if $info.cl_pref=="長野県"}selected{/if}>長野県</option>
            <option value="岐阜県" {if $info.cl_pref=="岐阜県"}selected{/if}>岐阜県</option>
            <option value="静岡県" {if $info.cl_pref=="静岡県"}selected{/if}>静岡県</option>
            <option value="愛知県" {if $info.cl_pref=="愛知県"}selected{/if}>愛知県</option>
            <option value="三重県" {if $info.cl_pref=="三重県"}selected{/if}>三重県</option>
            <option value="滋賀県" {if $info.cl_pref=="滋賀県"}selected{/if}>滋賀県</option>
            <option value="京都府" {if $info.cl_pref=="京都府"}selected{/if}>京都府</option>
            <option value="大阪府" {if $info.cl_pref=="大阪府"}selected{/if}>大阪府</option>
            <option value="兵庫県" {if $info.cl_pref=="兵庫県"}selected{/if}>兵庫県</option>
            <option value="奈良県" {if $info.cl_pref=="奈良県"}selected{/if}>奈良県</option>
            <option value="和歌山県" {if $info.cl_pref=="和歌山県"}selected{/if}>和歌山県</option>
            <option value="鳥取県" {if $info.cl_pref=="鳥取県"}selected{/if}>鳥取県</option>
            <option value="島根県" {if $info.cl_pref=="島根県"}selected{/if}>島根県</option>
            <option value="岡山県" {if $info.cl_pref=="岡山県"}selected{/if}>岡山県</option>
            <option value="広島県" {if $info.cl_pref=="広島県"}selected{/if}>広島県</option>
            <option value="山口県" {if $info.cl_pref=="山口県"}selected{/if}>山口県</option>
            <option value="徳島県" {if $info.cl_pref=="徳島県"}selected{/if}>徳島県</option>
            <option value="香川県" {if $info.cl_pref=="香川県"}selected{/if}>香川県</option>
            <option value="愛媛県" {if $info.cl_pref=="愛媛県"}selected{/if}>愛媛県</option>
            <option value="高知県" {if $info.cl_pref=="高知県"}selected{/if}>高知県</option>
            <option value="福岡県" {if $info.cl_pref=="福岡県"}selected{/if}>福岡県</option>
            <option value="佐賀県" {if $info.cl_pref=="佐賀県"}selected{/if}>佐賀県</option>
            <option value="長崎県" {if $info.cl_pref=="長崎県"}selected{/if}>長崎県</option>
            <option value="熊本県" {if $info.cl_pref=="熊本県"}selected{/if}>熊本県</option>
            <option value="大分県" {if $info.cl_pref=="大分県"}selected{/if}>大分県</option>
            <option value="宮崎県" {if $info.cl_pref=="宮崎県"}selected{/if}>宮崎県</option>
            <option value="鹿児島県" {if $info.cl_pref=="鹿児島県"}selected{/if}>鹿児島県</option>
            <option value="沖縄県" {if $info.cl_pref=="沖縄県"}selected{/if}>沖縄県</option>
        </select>
      {if form_error('cl_pref')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_pref')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_addr01" class="col-md-3 control-label">市区町村<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cl_addr01' , set_value('cl_addr01', $info.cl_addr01) , 'class="form-control p-locality" placeholder="市区町村を入力してください。 max.100文字"')}
      {if form_error('cl_addr01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_addr01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_addr02" class="col-md-3 control-label">町名・番地<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cl_addr02' , set_value('cl_addr02', $info.cl_addr02) , 'class="form-control p-street-address" placeholder="町名・番地を入力してください。 max.100文字"')}
      {if form_error('cl_addr02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_addr02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_buil" class="col-md-3 control-label">ビル・マンション名など</label>
    <div class="col-md-8">
      {form_input('cl_buil' , set_value('cl_buil', $info.cl_buil) , 'class="form-control p-extended-address" placeholder="ビル・マンション名などを入力してください。 max.100文字"')}
      {if form_error('cl_buil')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_buil')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_president" class="col-md-3 control-label">代表者<font color=red> *</font></label>
    <div class="col-md-4">
      {form_input('cl_president01' , set_value('cl_president01', $info.cl_president01) , 'class="form-control" placeholder="代表者姓を入力してください"')}
      {if form_error('cl_president01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_president01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('cl_president02' , set_value('cl_president02', $info.cl_president02) , 'class="form-control" placeholder="代表者名を入力してください"')}
      {if form_error('cl_president02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_president02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_tel01" class="col-xs-3 col-md-3 control-label">代表電話番号<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
      {form_input('cl_tel01' , set_value('cl_tel01', $info.cl_tel01) , 'class="form-control" placeholder="代表電話番号を入力してください"')}
      {if form_error('cl_tel01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_tel01')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
  <label for="cl_department" class="col-md-3 control-label">担当所属部署</label>
    <div class="col-md-8">
      {form_input('cl_department' , set_value('cl_department', $info.cl_department) , 'class="form-control" placeholder="所属部署を入力してください"')}
      {if form_error('cl_department')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_department')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_person" class="col-md-3 control-label">担当者<font color=red> *</font></label>
    <div class="col-md-4">
      {form_input('cl_person01' , set_value('cl_person01', $info.cl_person01) , 'class="form-control" placeholder="担当者姓を入力してください"')}
      {if form_error('cl_person01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_person01')}</font></label>{/if}
    </div>
    <div class="col-md-4">
      {form_input('cl_person02' , set_value('cl_person02', $info.cl_person02) , 'class="form-control" placeholder="担当者名を入力してください"')}
      {if form_error('cl_person02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_person02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_tel02" class="col-xs-3 col-md-3 control-label">担当者電話番号<font color=red> *</font></label>
    <div class="col-xs-4 col-md-4">
      {form_input('cl_tel02' , set_value('cl_tel02', $info.cl_tel02) , 'class="form-control" placeholder="担当者電話番号を入力してください"')}
      {if form_error('cl_tel02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_tel02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_mobile" class="col-xs-3 col-md-3 control-label">担当者携帯番号</label>
    <div class="col-xs-4 col-md-4">
      {form_input('cl_mobile' , set_value('cl_mobile', $info.cl_mobile) , 'class="form-control" placeholder="担当者携帯番号を入力してください"')}
      {if form_error('cl_mobile')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_mobile')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_fax" class="col-xs-3 col-md-3 control-label">FAX番号</label>
    <div class="col-xs-4 col-md-4">
      {form_input('cl_fax' , set_value('cl_fax', $info.cl_fax) , 'class="form-control" placeholder="FAX番号を入力してください"')}
      {if form_error('cl_fax')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_fax')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_mail" class="col-md-3 control-label">メールアドレス<font color=red> *</font></label>
    <div class="col-md-8">
      {form_input('cl_mail' , set_value('cl_mail', $info.cl_mail) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください"')}
      {if form_error('cl_mail')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_mail')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_mailsub" class="col-md-3 control-label">メールアドレス(サブ)</label>
    <div class="col-md-8">
      {form_input('cl_mailsub' , set_value('cl_mailsub', $info.cl_mailsub) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください"')}
      {if form_error('cl_mailsub')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_mailsub')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="cl_memo" class="col-sm-3 control-label">備　　考</label>
    <div class="col-md-8">
      <textarea class="form-control input-sm" id="cl_memo" name="cl_memo" placeholder="max.1000文字">{$info.cl_memo}</textarea>
      {if form_error('cl_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_memo')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('cl_seq', $info.cl_seq)}

  <!-- Button trigger modal -->
  <div class="row">
  <div class="col-sm-2 col-sm-offset-4">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>
  </div>
  </div>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">クライアント情報　更新</h4>
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
