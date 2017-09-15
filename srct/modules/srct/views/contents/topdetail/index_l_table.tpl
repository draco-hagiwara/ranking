{*** 左サイド ***}
{if $tabs=="rd"}

	{** ルートドメイン一覧 **}

	{$cnt=0}
	{foreach from=$list_catalog item=kw name="cnt"}
	  <p class="rd_name text-left" id='{$cnt}'><b>{$kw.kw_rootdomain}</b></p>
	  {$cnt=$cnt+1}
	{foreachelse}
	  ルートドメイン情報がありません。
	{/foreach}

{else}

	{** グループ一覧 **}

	{$cnt=0}
	{foreach from=$list_catalog item=kw name="cnt"}
	  <p class="gt_name text-left" id='{$cnt}'><b>{$kw.kw_group}</b></p>
	  {$cnt=$cnt+1}
	{foreachelse}
	  グループ情報がありません。
	{/foreach}

{/if}


{* 「ルートドメイン選択」or「グループ選択」から右ブロックにキーワード情報を表示 *}
{include file="../../../../../public/js/my/top_leftarea_select.php"}

