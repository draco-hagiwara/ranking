<span>以下のキーワード情報を削除します。<br>よろしいですか。</span><br><br>

{section name=item loop=$list}
  {section name=unit loop=$list[item]}
    <ul>
	  <li type="sqare">【キーワード】{$list[item][unit].kw_keyword}, 【URL】{$list[item][unit].kw_url}</li>
    </ul>
  {/section}
{/section}

<div id="result_delete"></div>

<br>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <div id="delete-save" class="btn btn-sm btn-primary">削　除</div>
</div>

<script>
// チェックボックスクリック ＆ キーワード削除ボタン で削除画面表示
$(function () {

	// 削除処理
	$('#delete-save').on('click', function () {

		// seqは複数指定あり
		var checkbox_kwseq = [];
	    checkbox_kwseq = "{$arr_kw_seq}";

	    $.ajax({
	        url           : '/srct/topdetail/index_aj_kwdelete_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : {
					        	kw_seq         : checkbox_kwseq,
					        	kw_cl_seq      : {$list[0][0].kw_cl_seq},
	        			    }
	    })
	    .done(function(response) {

	    	for(var i=0; i<response.length; i++) {
	    	   	if (response[i].title == "success_delete") {

	    	   		// Modal画面の削除
		    		$('#kw_delete').modal('hide');

		    		// TOP画面のリロード
		    		//location.reload();

	    			// TOP画面を更新(左サイド)
	    		    $('#result_list_tb').load('/srct/topdetail/index_aj_area/',{
	    	    		'area'         : "left",
	    		    	'kw_seq'       : checkbox_kwseq,
	    			});

	    			// TOP画面を更新(右サイド)
	    		    $('#result_rank_tb').load('/srct/topdetail/index_aj_area/',{
	    	    		'area'         : "right",
	    		    	'kw_seq'       : checkbox_kwseq,
	    			});
	    	   	} else {
	    	   		$('#result_delete').empty();
	    	   		$('#result_delete').append(response[i].message);
	    	   		$("#result_delete").css("color","red");
	    	   	}
	    	}

	    })
	    .fail(function() {
	        alert('通信エラー');
	    });

	});
});
</script>
