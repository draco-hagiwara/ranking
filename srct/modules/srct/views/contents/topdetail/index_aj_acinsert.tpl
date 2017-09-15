<form>

  {$mess}
  <div class="form-group">
    <label for="ac_type" class="control-label">アカウント選択<font color=red> *</font></label>
    <div class="btn-xs">
      {form_dropdown('ac_type', $options_ac_type, set_value('ac_type', ''), 'id="ac_type"')}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_status" class="control-label">ステータス選択<font color=red> *</font></label>
    <div class="btn-xs">
      {form_dropdown('ac_status', $options_ac_status, set_value('ac_status', ''), 'id="ac_status"')}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_department" class="control-label">所属部署</label>
    <div>
      {form_input('ac_department' , set_value('ac_department', '') , 'id="ac_department" class="form-control" placeholder="所属部署を入力してください。max.50文字"')}
      <div id="result_insert_department"></div>
    </div>
  </div>
  <div class="form-group">
    <label for="ac_name" class="control-label">担当者<font color=red> *</font></label>
    <div>
      {form_input('ac_name01' , set_value('ac_name01', '') , 'id="ac_name01" class="form-control" placeholder="担当者姓を入力してください。max.50文字"')}
      <div id="result_insert_name01"></div>
    </div>
    <div>
      {form_input('ac_name02' , set_value('ac_name02', '') , 'id="ac_name02" class="form-control" placeholder="担当者名を入力してください。max.50文字"')}
      <div id="result_insert_name02"></div>
    </div>
  </div>
  <div class="form-group">
    <label for="ac_id" class="control-label">メールアドレス＆ログインID<font color=red> *</font></label>
    <div>
      {form_input('ac_id' , set_value('ac_id', '') , 'id="ac_id" class="col-md-4 form-control" placeholder="メールアドレスを入力してください。max.50文字"')}
      <p class="redText"><small>※メールアドレス(英数字、アンダースコア(_)、ダッシュ(-))を入力してください。 max.50文字</small></p>
      <div id="result_insert_id"></div>
    </div>
  </div>
  <div class="form-group">
    <label for="ac_pw" class="control-label">パスワード<font color=red> *</font></label>
    <div>
      {form_password('ac_pw' , set_value('ac_pw', '') , 'id="ac_pw" class="form-control" placeholder="パスワード　(半角英数字・記号：８文字以上)。max.50文字"')}
      <p class="redText"><small>※お客様のお名前や、生年月日、またはその他の個人情報など、推測されやすい情報は使用しないでください。</small></p>
      <div id="result_insert_pw"></div>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label">権限の付与<font color=red> *</font></label>
    <div>■&emsp;キーワード</div>
    <div>
      <label class="radio-target">
        <input type="radio" name="ac_keyword" id="radio-keyword1" value="1"> 権限あり
      </label>
      <label class="radio-target">&emsp;&emsp;
        <input type="radio" name="ac_keyword" id="radio-keyword0" value="0"> 権限なし
      </label>
      <div id="result_radio_keyword"></div>
    </div>
    <div>■&emsp;グループ</div>
    <div>
      <label class="radio-target">
        <input type="radio" name="ac_group" id="radio-group1" value="1"> 権限あり
      </label>
      <label class="radio-target">&emsp;&emsp;
        <input type="radio" name="ac_group" id="radio-group0" value="0"> 権限なし
      </label>
      <div id="result_radio_group"></div>
    </div>
  </div>

  <div id="result_insert"></div>

  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<div id="insert-save" class="btn btn-sm btn-primary">追　加</div>
  </div>

</form>


<script>
// アカウント追加 & バリデーションチェック
$(function () {

	// 追加処理
	$('#insert-save').on('click', function () {

		// 権限の付与
		if ($('#radio-keyword1:checked').val())
		{
			ac_keyword = $('#radio-keyword1:checked').val();
		} else {
			ac_keyword = $('#radio-keyword0:checked').val();
		}

		if ($('#radio-group1:checked').val())
		{
			ac_group = $('#radio-group1:checked').val();
		} else {
			ac_group = $('#radio-group0:checked').val();
		}

		$('#result_insert_department').empty();
   		$('#result_insert_name01').empty();
   		$('#result_insert_name02').empty();
   		$('#result_insert_id').empty();
   		$('#result_insert_pw').empty();
   		$('#result_radio_keyword').empty();
   		$('#result_radio_group').empty();

	    $.ajax({
	        url           : '/srct/topdetail/index_aj_acinsert_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
					        	ac_type       : $("#ac_type").val(),
					        	ac_status     : $('#ac_status').val(),
								ac_department : $("#ac_department").val(),
								ac_name01     : $('#ac_name01').val(),
								ac_name02     : $('#ac_name02').val(),
								ac_id         : $('#ac_id').val(),
								ac_pw         : $('#ac_pw').val(),
								ac_keyword    : ac_keyword,
								ac_group      : ac_group,
					        }
	    })
	    .done(function(response) {

	    	for(var i=0; i<response.length; i++) {
	    	   	if (response[i].title == "department") {
	    	   		$('#result_insert_department').append(response[i].message);
	    	   		$("#result_insert_department").css("color","red");
		        } else if (response[i].title == "name01") {
	    	   		$('#result_insert_name01').append(response[i].message);
	    	   		$("#result_insert_name01").css("color","red");
		        } else if (response[i].title == "name02") {
	    	   		$('#result_insert_name02').append(response[i].message);
	    	   		$("#result_insert_name02").css("color","red");
		        } else if (response[i].title == "id") {
	    	   		$('#result_insert_id').append(response[i].message);
	    	   		$("#result_insert_id").css("color","red");
	    	   	} else if (response[i].title == "pw") {
	    	   		$('#result_insert_pw').append(response[i].message);
	    	   		$("#result_insert_pw").css("color","red");
	    	   	} else if (response[i].title == "radio_keyword") {
	    	   		$('#result_radio_keyword').append(response[i].message);
	    	   		$("#result_radio_keyword").css("color","red");
	    	   	} else if (response[i].title == "radio_group") {
	    	   		$('#result_radio_group').append(response[i].message);
	    	   		$("#result_radio_group").css("color","red");
	    	   	} else {

	    			// Modal画面の削除
	    			$('#ac_insert').modal('hide');

	    			// 画面のリロード
	    			//location.reload();

	    			// Modalに編集画面を表示
	    		    $('#result_account_table').load('/srct/topdetail/index_aj_accountlist/',{
	    			});
	    	   	}
	    	}

	    })
	    .fail(function() {
	        alert('通信エラー');
	    });
	});
});
</script>

