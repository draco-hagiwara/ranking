


{form_open('topdetail/chg_chk/' , 'name="detailForm" class="form-horizontal repeater"')}

  {$mess}
  <div class="form-group">
    <label for="kw_status" class="control-label">ステータス選択<font color=red> *</font></label>
    <div class="btn-md">
      {form_dropdown('kw_status', $options_kw_status, set_value('kw_status', $info.kw_status), 'id="kw_status"')}
    </div>
  </div>

  <div class="form-group">
    <label class="control-label">対象キーワード設定</label>
  </div>

  <div class="form-group">
    <div>■ 検索キーワード<font color=red> *</font>：</div>
    <div>
      {foreach from=$arr_keyword item=kwname name="cnt"}
        {$kwname}<br>
      {/foreach}
    </div>
  </div>
  <div class="form-group">
    <div>■ 対象URL<font color=red> *</font>：</div>
    <div>
        {form_input('kw_url' , set_value('kw_url', $info.kw_url) , 'id="kw_url_update" class="form-control" placeholder="日本語URLの場合はエンコードしてから入力してください。http(s)://～ max.510文字。"')}
        ※URLを変更する場合、旧URLの順位データを引き継ぎます。<br>
        <div id="result_update_kwurl"></div>
    </div>
  </div>
  <div class="form-group">
    <div>■ URLマッチタイプ<font color=red> *</font>：</div>
    <div>
        <label class="radio-match">
          <input type="radio" name="kw_matchtype" id="radio-match0" value="0" {if $info.kw_matchtype==0}checked{/if}> 完全一致
        </label>
        <label class="radio-match">&emsp;&emsp;
          <input type="radio" name="kw_matchtype" id="radio-match1" value="1" {if $info.kw_matchtype==1}checked{/if}> 前方一致
        </label>
        <label class="radio-match">&emsp;&emsp;
          <input type="radio" name="kw_matchtype" id="radio-match2" value="2" {if $info.kw_matchtype==2}checked{/if}> ドメイン一致
        </label>
        <label class="radio-match">&emsp;&emsp;
          <input type="radio" name="kw_matchtype" id="radio-match3" value="3" {if $info.kw_matchtype==3}checked{/if}> ルートドメイン一致 (サブドメイン含む)
        </label>
    </div>
  </div>

  {* Group ＆ Tag設定 *}
  <div class="form-group">
    <label for="kw_group" class="control-label">グループ設定</label>
    <div>
      <select multiple="multiple" name="kw_group[]" id="select2kwgroup" style="width: 400px;">
        {$options_group}
      </select>
    </div>
  </div>

<script type="text/javascript">
$(function() {
  $("#select2kwgroup").select2({
	tags: true,
	maximumSelectionLength: 1,
  });
});
</script>

	<div id="result_update"></div>

  <br>

  <div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<div id="update-save" class="btn btn-sm btn-primary">更　新</div>
  </div>

{form_close()}

<!-- </form> -->



<script>
// チェックボックスクリック ＆ キーワード削除ボタン で削除画面表示
$(function () {

	// 対象URLのバリデーションチェック
	$("#kw_url_update").blur(function () {

		var seturl = $('#kw_url_update').val();
		console.log(seturl);


	    $.ajax({
	        url           : '/srct/topdetail/index_aj_kwupdate_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
		            			key_item : "kw_url",
					            name     : seturl,
	        			    }
	    })
	    .done(function(response) {

	    	console.log(response);

    	    for(var i=0; i<response.length; i++) {
    	    	if (response[i].title == "対象URL") {
    	    		$('#result_update_kwurl').empty();
    	    		$('#result_update_kwurl').append(response[i].message);
    	    		$("#result_update_kwurl").css("color","red");
    	    	}
    	    }

	        //$('#result_update_kwurl').text(response);
	        //$("#result_update_kwurl").css("color","red");
	    })
	    .fail(function() {
	        alert('通信エラー');
	    });
	});

	// 更新処理
	$('#update-save').on('click', function () {

		// Modal画面の削除
		//$('#kw_update').modal('hide');

		// Modal画面の起動
	    //$('#kw_update_chk').modal();

		// seqは複数指定あり
		var checkbox_kwseq = [];
	    checkbox_kwseq = "{$arr_kw_seq}";

		// URLマッチタイプ：↓何故かデフォルトで"3"が選択される？ この順番が大事！
		if ($('#radio-match0:checked').val())
		{
			kw_matchtype = $('#radio-match0:checked').val();
		} else if ($('#radio-match1:checked').val()) {
			kw_matchtype = $('#radio-match1:checked').val();
		} else if ($('#radio-match2:checked').val()) {
			kw_matchtype = $('#radio-match2:checked').val();
		} else {
			kw_matchtype = $('#radio-match3:checked').val();

		    //'kw_matchtype': $('#radio-match0:checked').val(),
		    //'kw_matchtype': $("input:radio[name='kw_matchtype']:checked").val(),
		    //'kw_matchtype': $('input[name="kw_matchtype"]:checked').val(),			// ←何故かデフォルトで"3"が選択される？

		}


	    $.ajax({
	        url           : '/srct/topdetail/index_aj_kwupdate_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
					        	kw_seq         : checkbox_kwseq,
					        	kw_status      : $("#kw_status").val(),
					        	kw_url         : $('#kw_url_update').val(),
					        	kw_group       : $('#select2kwgroup').val(),
					        	kw_matchtype   : kw_matchtype,
					        	kw_location_id : {$info.kw_location_id},
					        	kw_cl_seq      : {$info.kw_cl_seq},
					        	kw_ac_seq      : {$info.kw_ac_seq},
	        			    }
	    })
	    .done(function(response) {

	    	console.log(response);

	    	//var len = response.length;
	    	//console.log(len);

	    	for(var i=0; i<response.length; i++) {
	    	   	if (response[i].title == "success_update") {
	    			// Modal画面の削除
	    			$('#kw_update').modal('hide');

	    			// 画面のリロード
	    			location.reload();
		        } else if (response[i].title == "対象URL") {
	    	   		$('#result_update_kwurl').empty();
	    	   		$('#result_update_kwurl').append(response[i].message);
	    	   		$("#result_update_kwurl").css("color","red");
	    	   	} else if (response[i].title == "結果") {
	    	   		$('#result_update').empty();
	    	   		$('#result_update').append(response[i].message);
	    	   		$("#result_update").css("color","red");
	    	   	}
	    	}

	    })
	    .fail(function() {
	        alert('通信エラー');
	    });

	});
});
</script>


