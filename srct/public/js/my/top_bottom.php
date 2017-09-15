<!--
 *
 * bottom : 順次読み込みjs
 *
-->
<script>
$(function ()
{

	var page = 0; 												// ページ番号
	var end_flag = 0; 											// 最後のページまでいったら1にして次から読み込ませない
	var per_page = {$per_page}									// 1ページ当たりの表示件数

	$("#bottom_rank_tb").bottom("proximity: 0"); 				// proximityを0.5にするとページの50％までスクロールするとloadingがはじまる

	$("#bottom_rank_tb").bind("bottom", function() {

		if(end_flag==0){ 										// ページが最後までいってなければ

	    	var obj = $(this);

	        if (!obj.data("loading")) {

	        	obj.data("loading", true);

	          	setTimeout(function() {
		      	    $.ajax({
		    	        url           : '/srct/topdetail/index_aj_bottom/',
		    	        type          : 'post',
		    	        //async         : false,        		// 同期にするとSynchronous XMLHttpRequestを使うな警告がでる）
		    	        dataType      : 'jsonp',
		    	        jsonpCallback : 'callback',
		    	        data          : {
		    	        					offset: ++page,
		    	        			    }
		    	    })
		    	    .done(function(response) {

		    	    	if (response != "end") {

	                        for(var i = 0; i < response.length; i++){

       	                        var strTR_start = '<tr>';

       	                        // ウォッチリスト
	       	                    var wt_seq_act;
	       	                    var watchlist_png;
	       	                    if (response[i].wt_seq) {
	       	                    	wt_seq_act = "active";
	       	                  	    watchlist_png = '<img id="watchlist_kw' + response[i].kw_seq + '" src="/images/user/wl_on.png" />';
	       	                    } else {
	       	                  	    wt_seq_act = "";
	       	                  	    watchlist_png = '<img id="watchlist_kw' + response[i].kw_seq + '" src="/images/user/wl.png" />';
	       	                    }

	       	                    // グループ名
		                        var kw_group_name;
		                        if (response[i].kw_group) {
		                        	kw_group_name = response[i].kw_group;
		                        } else {
		                        	kw_group_name = "-";
		                        }

		                        // マッチタイプ
		                        var kw_matchtype_tag;
		                        if (response[i].kw_matchtype==0) {
		                        	kw_matchtype_tag = '<img src="/images/user/p1.png" title="完全一致" />';
		                        } else if (response[i].kw_matchtype==1) {
		                        	kw_matchtype_tag = '<img src="/images/user/p2.png" title="前方一致" />';
		                        } else if (response[i].kw_matchtype==2) {
		                        	kw_matchtype_tag = '<img src="/images/user/p3.png" title="ドメイン一致" />';
		                        } else if (response[i].kw_matchtype==3) {
		                        	kw_matchtype_tag = '<img src="/images/user/p4.png" title="ルートドメイン一致" />';
		                        } else {
		                        	kw_matchtype_tag = 'error';
		                        }

		                        // Google-pc ランクセット
		                        var updown_yesterday00;
		                        var arrow_yesterday00;
		                        if (response[i].comp_yesterday00 > 0) {
		                        	updown_yesterday00 = "up";
			                        arrow_yesterday00  = "↑";
		                        } else if (response[i].comp_yesterday00 < 0) {
		                        	updown_yesterday00 = "down";
			                        arrow_yesterday00  = "↓";
		                        } else {
		                        	updown_yesterday00 = "same";
			                        arrow_yesterday00  = "";
		                        }

		                        var updown_compweek00;
		                        var arrow_compweek00;
		                        if (response[i].comp_week00 > 0) {
		                        	updown_compweek00 = "up";
		                        	arrow_compweek00  = "↑";
		                        } else if (response[i].comp_week00 < 0) {
		                        	updown_compweek00 = "down";
		                        	arrow_compweek00  = "↓";
		                        } else {
		                        	updown_compweek00 = "same";
		                        	arrow_compweek00  = "";
		                        }

		                        var updown_compmonth00;
		                        var arrow_compmonth00;
		                        if (response[i].comp_month00 > 0) {
		                        	updown_compmonth00 = "up";
		                        	arrow_compmonth00  = "↑";
		                        } else if (response[i].comp_month00 < 0) {
		                        	updown_compmonth00 = "down";
		                        	arrow_compmonth00  = "↓";
		                        } else {
		                        	updown_compmonth00 = "same";
		                        	arrow_compmonth00  = "";
		                        }

		                        // Google-mobile ランクセット
		                        var updown_yesterday01;
		                        var arrow_yesterday01;
		                        if (response[i].comp_yesterday01 > 0) {
		                        	updown_yesterday01 = "up";
		                        	arrow_yesterday01  = "↑";
		                        } else if (response[i].comp_yesterday01 < 0) {
		                        	updown_yesterday01 = "down";
		                        	arrow_yesterday01  = "↓";
		                        } else {
		                        	updown_yesterday01 = "same";
		                        	arrow_yesterday01  = "";
		                        }

		                        // Yahoo!-pc ランクセット
		                        var updown_yesterday10;
		                        var arrow_yesterday10;
		                        if (response[i].comp_yesterday10 > 0) {
		                        	updown_yesterday10 = "up";
		                        	arrow_yesterday10  = "↑";
		                        } else if (response[i].comp_yesterday10 < 0) {
		                        	updown_yesterday10 = "down";
		                        	arrow_yesterday10  = "↓";
		                        } else {
		                        	updown_yesterday10 = "same";
		                        	arrow_yesterday10  = "";
		                        }

		                        // チェックボックス用連続カウンターを計算
		                        var cnt = (per_page + i) + ((page - 1) * per_page);

		                        // テーブルを整形する
		                        var strTD =
             								'<td class="checkbox_kw' + cnt + ' t_ck"><input type="checkbox" name="checkbox_kwseq[]" value="' + response[i].kw_seq + '"></td>'
             								+ '<td><label class="btn_wk' + page + ' ' + wt_seq_act + ' data-text-default="☆" data-text-clicked="★" data-kw-seq=' + response[i].kw_seq + ">" +  watchlist_png + '</label></td>'
             								+ '<td class="jqPlot-show' + page + ' t_gr" id="' + response[i].kw_seq + '">' + kw_group_name + '</td>'
             								+ '<td class="jqPlot-show' + page + ' t_kw" id="' + response[i].kw_seq + '">' + response[i].kw_keyword + '</td>'
             								+ '<td class="f_ur"><a href="' + response[i].kw_url + '" target="_blank">' + response[i].kw_url + '</a></td>'
             								+ '<td class="jqPlot-show' + page + ' f_mt" id="' + response[i].kw_seq + '">' + kw_matchtype_tag + '</td>'
             								+ '<td class="jqPlot-show' + page + ' t_lo" id="' + response[i].kw_seq + '" title="' + response[i].kw_location_name + '">' + response[i].kw_location_short + '</td>'

             								+ '<td class="jqPlot-show' + page + ' f_rk" id="' + response[i].kw_seq + '">' + response[i].comp_today00 + '</td>'
             								+ '<td class="jqPlot-show' + page + ' f_po" id="' + response[i].kw_seq + '"><span class=' + updown_yesterday00 + '>' + arrow_yesterday00 + response[i].comp_yesterday00 +'</span></td>'
             								+ '<td class="jqPlot-show' + page + ' f_po" id="' + response[i].kw_seq + '"><span class=' + updown_compweek00 + '>' + arrow_compweek00 + response[i].comp_week00 +'</span></td>'
             								+ '<td class="jqPlot-show' + page + ' f_po" id="' + response[i].kw_seq + '"><span class=' + updown_compmonth00 + '>' + arrow_compmonth00 + response[i].comp_month00 +'</span></td>'

             								+ '<td class="jqPlot-show' + page + ' f_rk" id="' + response[i].kw_seq + '">' + response[i].comp_today01 + '</td>'
             								+ '<td class="jqPlot-show' + page + ' f_po" id="' + response[i].kw_seq + '"><span class=' + updown_yesterday01 + '>' + arrow_yesterday01 + response[i].comp_yesterday01 +'</span></td>'

             								+ '<td class="jqPlot-show' + page + ' f_rk" id="' + response[i].kw_seq + '">' + response[i].comp_today10 + '</td>'
             								+ '<td class="jqPlot-show' + page + ' f_po" id="' + response[i].kw_seq + '"><span class=' + updown_yesterday10 + '>' + arrow_yesterday10 + response[i].comp_yesterday10 +'</span></td>'
             					;

             					var strTR_end = '</tr>';

             					var str = strTR_start + strTD + strTR_end;

		                        // テーブルへ追加
		                        $(".bottom_rank_tb").append(str);

		                        // グラフ表示領域
		                        var strGraph =
		                        			'<tr>'
		                	    			+ '<td colspan=15 class="jqPlot-area' + cnt + '" style="display:none;">'
		                	      			+ '<div id="result_jqPlot' + cnt + '"></td>'
                      			;

   		                        $(".bottom_rank_tb").append(strGraph + strTR_end);

		                    }

	    	                // JS再読み込み
	    	                {include file="./top_bottom_jqplot_show.php"}
	    	                {include file="./top_bottom_watchlist.php"}

		    	    	} else {
		    	    		end_flag = 1;
		                }
		    	    })
		    	    .fail(function() {
		    	        alert('通信エラー');
		    	    });

		            obj.data("loading", false);
	         	}, 10);
	        }
		}
	});
});

</script>
