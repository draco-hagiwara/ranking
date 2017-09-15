// ウォッチリスト
$('.wl_sort_cl').on('click', function () {

	$(this).empty();

    $(this).toggleClass('active');

    if($(this).hasClass('active')){
	    var tmp_sort = "DESC";
    } else {
	    var tmp_sort = "ASC";
    }

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "left",
			'tmp_item': "wl",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "left",
			'tmp_item': "group",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "left",
			'tmp_item': "keyword",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "left",
			'tmp_item': "url",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "left",
			'tmp_item': "location",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_kwsort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_ranksort/',{
			'area'    : "left",
			'tmp_item': "gpc",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_ranksort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_ranksort/',{
			'area'    : "left",
			'tmp_item': "gmo",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_ranksort/',{
			'area'    : "right",
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

	// TOP画面を更新(左サイド)
	$('#result_list_tb').load('/srct/topdetail/index_aj_ranksort/',{
			'area'    : "left",
			'tmp_item': "ypc",
			'tmp_sort': tmp_sort,
	});

	// TOP画面を更新(右サイド)
	$('#result_rank_tb').load('/srct/topdetail/index_aj_ranksort/',{
			'area'    : "right",
			'tmp_item': "ypc",
			'tmp_sort': tmp_sort,
	});
});