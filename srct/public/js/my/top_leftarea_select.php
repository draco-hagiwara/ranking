<!--
 * 「ルートドメイン選択」or「グループ選択」から右ブロックにキーワード情報を表示js
-->
<script>
$(function () {

	$(".rd_name").click(function () {

    	var id_idx = $(this).attr('id');

    	id_idx = '#' + id_idx;
    	var rootdomain = $( id_idx ).text();

    	$('#result_list_tb').load('/srct/topdetail/index_aj_rd/',{
    		'area'         : "left",
    		'kw_rootdomain': rootdomain,
    	});

    	$('#result_rank_tb').load('/srct/topdetail/index_aj_rd/',{
    		'area'         : "right",
    		'kw_rootdomain': rootdomain,
    	});

    	$(".f_keyword").val("");
    });
});

// グループ選択から右ブロックにキーワード情報を表示
$(function () {

	$(".gt_name").click(function () {

    	var id_idx = $(this).attr('id');
    	console.log(id_idx);

    	id_idx = '#' + id_idx;
    	var group = $( id_idx ).text();
    	console.log(group);

    	$('#result_list_tb').load('/srct/topdetail/index_aj_group/',{
    		'area'         : "left",
    		'kw_group': group,
    	});

    	$('#result_rank_tb').load('/srct/topdetail/index_aj_group/',{
    		'area'         : "right",
    		'kw_group': group,
    	});

    	$(".f_keyword").val("");
    });
});
</script>
