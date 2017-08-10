<!--
 * テーブルヘッダをマウスクリックによる昇順&降順 切替操作script
 *
 * 「ウォッチリスト」「グループ」「キーワード」「URL」「地域」「Google-pc」「Google-mobile」「Yahoo!-pc」
 *
-->
<script>
$(function () {

	// ウォッチリスト
	$('.wl_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		// 左サイド「キーワード一覧」を更新
		$('#result_rank_tb').load('/srct/topdetail/index_aj_wlsort/',{
				'tmp_item': "wl",
				'tmp_sort': tmp_sort,
		});
	 });

	// グループ
	$('.group_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		$('#result_rank_tb').load('/srct/topdetail/index_aj_groupsort/',{
				'tmp_item': "group",
				'tmp_sort': tmp_sort,
		});
	 });

	// キーワード
	$('.keyword_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		$('#result_rank_tb').load('/srct/topdetail/index_aj_keywordsort/',{
				'tmp_item': "keyword",
				'tmp_sort': tmp_sort,
		});
	 });

	// URL
	$('.url_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		$('#result_rank_tb').load('/srct/topdetail/index_aj_urlsort/',{
				'tmp_item': "url",
				'tmp_sort': tmp_sort,
		});
	 });

	// location
	$('.location_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		$('#result_rank_tb').load('/srct/topdetail/index_aj_locationsort/',{
				'tmp_item': "location",
				'tmp_sort': tmp_sort,
		});
	 });

	// Google-PC ranking
	$('.gpc_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		$('#result_rank_tb').load('/srct/topdetail/index_aj_gpcsort/',{
				'tmp_item': "gpc",
				'tmp_sort': tmp_sort,
		});
	 });

	// Google-Mobile ranking
	$('.gmo_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		$('#result_rank_tb').load('/srct/topdetail/index_aj_gmosort/',{
				'tmp_item': "gmo",
				'tmp_sort': tmp_sort,
		});
	 });

	// Yahoo!-PC ranking
	$('.ypc_sort_cl').on('click', function () {

		$(this).empty();

	    $(this).toggleClass('active');

	    if($(this).hasClass('active')){
		    var tmp_sort = "DESC";
	    } else {
		    var tmp_sort = "ASC";
	    }

		console.log(tmp_sort);

		$('#result_rank_tb').load('/srct/topdetail/index_aj_ypcsort/',{
				'tmp_item': "ypc",
				'tmp_sort': tmp_sort,
		});
	 });

});
</script>

