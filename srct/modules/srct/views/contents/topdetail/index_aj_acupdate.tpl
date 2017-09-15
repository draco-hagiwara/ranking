<form>

  {$mess}
  {if $smarty.session.c_memType==0}
  <div class="form-group">
    <label for="ac_type" class="control-label">アカウント種類選択<font color=red> *</font></label>
    <div class="btn-xs">
      {form_dropdown('ac_type', $options_ac_type, set_value('ac_type', $info.ac_type), 'id="ac_type"')}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_status" class="control-label">ステータス選択<font color=red> *</font></label>
    <div class="btn-xs">
      {form_dropdown('ac_status', $options_ac_status, set_value('ac_status', $info.ac_status), 'id="ac_status"')}
    </div>
  </div>
  {/if}

  <div class="form-group">
    <label for="ac_department" class="control-label">所属部署</label>
    <div>
      {form_input('ac_department' , set_value('ac_department', $info.ac_department) , 'id="ac_department" class="form-control" placeholder="所属部署を入力してください。max.50文字"')}
      <div id="result_insert_department"></div>
    </div>
  </div>
  <div class="form-group">
    <label for="ac_name" class="control-label">担当者<font color=red> *</font></label>
    <div>
      {form_input('ac_name01' , set_value('ac_name01', $info.ac_name01) , 'id="ac_name01" class="form-control" placeholder="担当者姓を入力してください。max.50文字"')}
      <div id="result_insert_name01"></div>
    </div>
    <div>
      {form_input('ac_name02' , set_value('ac_name02', $info.ac_name02) , 'id="ac_name02" class="form-control" placeholder="担当者名を入力してください。max.50文字"')}
      <div id="result_insert_name02"></div>
    </div>
  </div>
  <div class="form-group">
    <label for="ac_id" class="control-label">ログインID</label>
    <div>
      {$info.ac_id}
    </div>
  </div>

  {if $smarty.session.c_memSeq==$info.ac_seq}
  <div class="form-group">
    <label for="ac_pw" class="control-label">パスワード</label>
    <div>
      {form_password('ac_pw' , set_value('ac_pw', '') , 'id="ac_pw" class="form-control" placeholder="パスワード　(半角英数字・記号：８文字以上)。max.50文字"')}
      <p class="redText"><small>※お客様のお名前や、生年月日、またはその他の個人情報など、推測されやすい情報は使用しないでください</small></p>
      <div id="result_insert_pw"></div>
    </div>
  </div>
  {/if}

  {if $smarty.session.c_memType==0}
  <div class="form-group">
    <label class="control-label">権限の付与<font color=red> *</font></label>
    <div>■&emsp;キーワード</div>
    <div>
      <label class="radio-target1">
        <input type="radio" name="ac_keyword" id="radio-keyword1" value="1" {if $info.ac_keyword==1}checked{/if}> 権限あり
      </label>
      <label class="radio-target2">&emsp;&emsp;
        <input type="radio" name="ac_keyword" id="radio-keyword0" value="0" {if $info.ac_keyword==0}checked{/if}> 権限なし
      </label>
    </div>
    <div>■&emsp;グループ</div>
    <div>
      <label class="radio-target2">
        <input type="radio" name="ac_group" id="radio-group1" value="1" {if $info.ac_group==1}checked{/if}> 権限あり
      </label>
      <label class="radio-target2">&emsp;&emsp;
        <input type="radio" name="ac_group" id="radio-group0" value="0" {if $info.ac_group==0}checked{/if}> 権限なし
      </label>
    </div>
  </div>
  {else}
  <div class="form-group">
    <label class="control-label">権限の付与</label>
    <div>■&emsp;キーワード：{if $info.ac_keyword==1}権限あり{else}権限なし{/if}</div>
    <div>■&emsp;グループ&emsp;：{if $info.ac_group==1}権限あり{else}権限なし{/if}</div>
  </div>

  <div id="result_update"></div>
  {/if}

  <br>

  <div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<div id="update-save" class="btn btn-sm btn-primary">更　新</div>
  </div>

</form>

<!-- </form> -->



<script>
// アカウント編集ボタン で画面表示
$(function () {

	// 更新処理
	$('#update-save').on('click', function () {

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

	    $.ajax({
	        url           : '/srct/topdetail/index_aj_acupdate_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
	        	ac_seq        : {$info.ac_seq},
	        	ac_type       : $("#ac_type").val(),
	        	ac_status     : $('#ac_status').val(),
				ac_department : $("#ac_department").val(),
				ac_name01     : $('#ac_name01').val(),
				ac_name02     : $('#ac_name02').val(),
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
	    	   	} else if (response[i].title == "pw") {
	    	   		$('#result_insert_pw').append(response[i].message);
	    	   		$("#result_insert_pw").css("color","red");
	    	   	} else {

	    	   		// Modal画面の削除
	    			$('#ac_update').modal('hide');

	    			// 画面のリロード
	    			//location.reload();

	    			// 画面を更新
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


