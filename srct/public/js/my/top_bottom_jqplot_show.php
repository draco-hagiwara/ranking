$(".jqPlot-show" + page).click(function () {

	//console.log("jqPlot-show = " + page);


	// idからキーワードseqを取得
	var kwseq_idx = $(this).attr('id');
	//console.log(kwseq_idx);

	// クリックされた行番号を取得
	var row = $(this).closest('td').parent()[0].sectionRowIndex
	var cnt = Math.floor(row/2);
	//console.log(cnt);

	//var id_idx = '#' + kwseq_idx;


	if($(".jqPlot-area" + cnt).hasClass('active')){

		$(".jqPlot-area" + cnt).toggleClass('active');
		//$(".jqPlot-area" + cnt).removeClass('active');

        $(".jqPlot-area" + cnt).slideUp();
	} else {

    	$(".jqPlot-area" + cnt).toggleClass('active');

    	// グラフ表示の有無

		$('#result_jqPlot' + cnt).load('/srct/topdetail/index_aj_jqPlot/',{
	    	'kw_seq': kwseq_idx,
	    	'cnt'   : cnt,
		});

    	$(".jqPlot-area" + cnt).slideDown();
	}

});
