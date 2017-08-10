<!--
 * グラフ表示script
 *
 * jsでデータを上手く受けられない？
 *
-->
<script>

jQuery( function() {
  $(".jqPlot-area{$cnt}").hide();

  $(".jqPlot-show{$cnt}").click(function () {

	console.log({$kw.kw_seq});
	console.log({$cnt});

    $('#result_jqPlot{$cnt}').load('/srct/topdetail/index_aj_jqPlot/',{
    		'kw_seq': {$kw.kw_seq},
    		'cnt': {$cnt},
    });

    if ($(".jqPlot-area{$cnt}").is(":hidden")) {
        $(".jqPlot-area{$cnt}").slideDown();
    } else {
        $(".jqPlot-area{$cnt}").slideUp();
    }

// 	$(".jqPlot-show{$cnt}").click(function () {
// 	    $(".jqPlot-area{$cnt}").slideToggle();
// 	    $(this).toggleClass('active');
// 	});

  });
});
</script>