        <form>
        {*form_open('searchrank/addchk/' , 'name="accountForm" class="form-horizontal repeater"')*}

		  {$mess}
		  <div class="form-group">
		    <label class="control-label">対象キーワード設定</label>
		    <div>■ 検索キーワード<font color=red> *</font>：</div>
		    <div>
		      <select multiple="multiple" name="kw_keyword[]" id="select2keyword" style="width: 400px;">
		        {$options_kw}
		      </select>
		    </div>
		    <div>
		      <small>※複数指定が可能。キーワード入力後、確定するにはENTERキーを押下してください。</small>
		      <div id="result_insert_keyword"></div>
		    </div>
		  </div>
		  <div class="form-group">
		    <div>■ 対象URL<font color=red> *</font>：</div>
		    <div>
		      {form_input('kw_url' , set_value('kw_url', '') , 'id="kw_url" class="form-control" placeholder="日本語URLの場合はエンコードしてから入力してください。http(s)://～ max.510文字。"')}
		      <div id="result_insert_url"></div>
		    </div>
		  </div>

		  <div class="form-group">
		    <div>■ URLマッチタイプ<font color=red> *</font>：</div>
		    <div>
		      <label class="radio-match">
		        <input type="radio" name="kw_matchtype" id="radio-match0" value="0" {if $url_match==0}checked{/if}> 完全一致
		      </label>
		      <label class="radio-match">&emsp;&emsp;
		        <input type="radio" name="kw_matchtype" id="radio-match1" value="1" {if $url_match==1}checked{/if}> 前方一致
		      </label>
		      <label class="radio-match">&emsp;&emsp;
		        <input type="radio" name="kw_matchtype" id="radio-match2" value="2" {if $url_match==2}checked{/if}> ドメイン一致
		      </label>
		      <label class="radio-match">&emsp;&emsp;
		        <input type="radio" name="kw_matchtype" id="radio-match3" value="3" {if $url_match==3}checked{/if}> ルートドメイン一致 (サブドメイン含む)
		      </label>
		    </div>
		  </div>
		  <div class="form-group">
		    <div>■ ロケーション指定<font color=red> *</font>：</div>
		    <div>
		      <select multiple="multiple" name="kw_location[]" id="select2location" style="width: 400px;">
		        {$options_location}
		      </select>
		    </div>
		    <div>
		      <small>※複数指定が可能。</small>
		      <div id="result_insert_location"></div>
		    </div>
		  </div>

		  <div class="form-group">
		    <label for="kw_group" class="control-label">グループ設定</label>
		    <div>
		      <select multiple="multiple" name="kw_group[]" id="select2group" style="width: 400px;">
		        {$options_group}
		      </select>
		    </div>
		  </div>

			<div id="result_insert"></div>

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <div id="insert-save" class="btn btn-sm btn-primary">追　加</div>
	      </div>

        </form>
        {*form_close()*}


{* SELECT入力フォームjs *}
<script src="{base_url()}../../js/my/kwlist_select2.js"></script>

{* 入力フォームのバリデーションチェック *}
{*include file="../../../../../public/js/my/top_validation_insert.php"*}



<script>
// チェックボックスクリック ＆ キーワード削除ボタン で削除画面表示
$(function () {

	// 検索キーワードのバリデーションチェック
	//
	//  ※※※ ここがどうしても値が取れない！！
	//
	$("#select2keyword").blur(function () {

		var setkeyword = $('#select2keyword').val();
		console.log(setkeyword);


	    $.ajax({
	        url           : '/srct/topdetail/index_aj_kwinsert_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
		            			key_item : "kw_keyword",
					            name     : setkeyword,
	        			    }
	    })
	    .done(function(response) {

	    	console.log(response);

    	    for(var i=0; i<response.length; i++) {
    	    	if (response[i].title == "検索キーワード") {
    	    		$('#result_insert_keyword').empty();
    	    		$('#result_insert_keyword').append(response[i].message);
    	    		$("#result_insert_keyword").css("color","red");
    	    	}
    	    }

	        //$('#result_update_kwurl').text(response);
	        //$("#result_update_kwurl").css("color","red");
	    })
	    .fail(function() {
	        alert('通信エラー');
	    });
	});

	// 対象URLのバリデーションチェック
	$("#kw_url").blur(function () {

		var seturl = $('#kw_url').val();
		console.log(seturl);


	    $.ajax({
	        url           : '/srct/topdetail/index_aj_kwinsert_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
		            			key_item : "kw_url",
					            name     : seturl,
	        			    }
	    })
	    .done(function(response) {

	    	//console.log(response);
    		$('#result_insert_url').empty();

    	    for(var i=0; i<response.length; i++) {
    	    	if (response[i].title == "対象URL") {
    	    		$('#result_insert_url').append(response[i].message);
    	    		$("#result_insert_url").css("color","red");
    	    	}
    	    }

	        //$('#result_update_kwurl').text(response);
	        //$("#result_update_kwurl").css("color","red");
	    })
	    .fail(function() {
	        alert('通信エラー');
	    });
	});

	// 追加処理
	$('#insert-save').on('click', function () {

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
		}

		$('#result_insert_keyword').empty();
   		$('#result_insert_url').empty();
   		$('#result_insert_location').empty();
   		$('#result_insert').empty();

	    $.ajax({
	        url           : '/srct/topdetail/index_aj_kwinsert_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
								kw_keyword   : $("#select2keyword").val(),
								kw_url       : $('#kw_url').val(),
								kw_matchtype : kw_matchtype,
								kw_location  : $("#select2location").val(),
								kw_group     : $('#select2group').val(),
					        }
	    })
	    .done(function(response) {

	    	//console.log(response);

	    	//var len = response.length;
	    	//console.log(len);


	    	for(var i=0; i<response.length; i++) {
	    	   	if (response[i].title == "success_insert") {
	    			// Modal画面の削除
	    			$('#kw_insert').modal('hide');

	    			// 画面のリロード
	    			location.reload();
		        } else if (response[i].title == "検索キーワード") {
	    	   		$('#result_insert_keyword').append(response[i].message);
	    	   		$("#result_insert_keyword").css("color","red");
		        } else if (response[i].title == "対象URL") {
	    	   		$('#result_insert_url').append(response[i].message);
	    	   		$("#result_insert_url").css("color","red");
		        } else if (response[i].title == "ロケーション指定") {
	    	   		$('#result_insert_location').append(response[i].message);
	    	   		$("#result_insert_location").css("color","red");
	    	   	} else if (response[i].title == "結果") {
	    	   		$('#result_insert').append(response[i].message);
	    	   		$("#result_insert").css("color","red");
	    	   	}
	    	}

	    })
	    .fail(function() {
	        alert('通信エラー');
	    });

	});
});
</script>

