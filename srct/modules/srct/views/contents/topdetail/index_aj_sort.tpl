{* 右サイトキーワード一覧テーブルのTPL呼び出し *}
{include file="./index_r_table.tpl"}

<script>
$(function () {

	if (("{$tmp_item}" == 'wl') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.wl_sort_cl').toggleClass('active');
        $("#wl_sort_id").append("▼");
	} else if (("{$tmp_item}" == 'wl') && ("{$tmp_sort}" == 'ASC')) {
        $("#wl_sort_id").append("▲");
	}

	if (("{$tmp_item}" == 'group') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.group_sort_cl').toggleClass('active');
        $("#group_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'group') && ("{$tmp_sort}" == 'ASC')) {
        $("#group_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'keyword') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.keyword_sort_cl').toggleClass('active');
        $("#keyword_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'keyword') && ("{$tmp_sort}" == 'ASC')) {
        $("#keyword_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'url') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.url_sort_cl').toggleClass('active');
        $("#url_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'url') && ("{$tmp_sort}" == 'ASC')) {
        $("#url_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'location') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.location_sort_cl').toggleClass('active');
        $("#location_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'location') && ("{$tmp_sort}" == 'ASC')) {
        $("#location_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'gpc') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.gpc_sort_cl').toggleClass('active');
        $("#gpc_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'gpc') && ("{$tmp_sort}" == 'ASC')) {
        $("#gpc_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'gmo') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.gmo_sort_cl').toggleClass('active');
        $("#gmo_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'gmo') && ("{$tmp_sort}" == 'ASC')) {
        $("#gmo_sort_id").append(" ▲");
	}

	if (("{$tmp_item}" == 'ypc') && ("{$tmp_sort}" == 'DESC'))
	{
		$('.ypc_sort_cl').toggleClass('active');
        $("#ypc_sort_id").append(" ▼");
	} else if (("{$tmp_item}" == 'ypc') && ("{$tmp_sort}" == 'ASC')) {
        $("#ypc_sort_id").append(" ▲");
	}

});
</script>
