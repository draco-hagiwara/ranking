{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}




<link href="{base_url()}../../js/jqcloud/jqcloud.css" rel="stylesheet">
<script src="{base_url()}../../js/jqcloud/jqcloud-1.0.4.min.js"></script>





<body>
{* ヘッダー部分　END *}

<script type="text/javascript">
<!--
function fmSubmit(formName, url, method, num) {
  var f1 = document.forms[formName];

  console.log(num);

  /* エレメント作成&データ設定&要素追加 */
  var e1 = document.createElement('input');
  e1.setAttribute('type', 'hidden');
  e1.setAttribute('name', 'chg_gtseq');
  e1.setAttribute('value', num);
  f1.appendChild(e1);

  /* サブミットするフォームを取得 */
  f1.method = method;                                   // method(GET or POST)を設定する
  f1.action = url;                                      // action(遷移先URL)を設定する
  f1.submit();                                          // submit する
  return true;
}
// -->
</script>

<div id="contents" class="container">

<h4>【タグ検索】</h4>
{form_open('/taglist/search/' , 'name="searchForm" class="form-horizontal"')}
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">タグ名</td>
        <td class="col-md-4">
          {form_input('gt_name' , set_value('gt_name', {$seach_gtname}) , 'class="form-control" placeholder="タグ名を入力してください。"')}
          {if form_error('gt_name')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('gt_name')}</font></label>{/if}
        </td>
        <td class="col-md-1">ID並び替え</td>
        <td class="col-md-2">
          {form_dropdown('orderid', $options_orderid, set_value('orderid', {$seach_orderid}))}
        </td>
      </tr>
    </tbody>
  </table>

  <div class="row">
    <div class="col-md-5 col-md-offset-5">
      {$attr['name']  = 'submit'}
      {$attr['type']  = 'submit'}
      {$attr['value'] = '_submit'}
      {form_button($attr , '検　　索' , 'class="btn btn-default"')}
    </div>
  </div>

{form_close()}

<ul class="pagination pagination-sm">
    検索結果： {$countall}件<br />
    {$set_pagination}
</ul>

{form_open('/taglist/detail/' , 'name="detailForm" class="form-horizontal"')}




<script>
var word_array = [
	{
		text: "Lorem", weight: 15
	},
	{
		text: "Ipsum", weight: 20, link: "../tag_test/"
	},
	{
		text: "Dolor", weight: 17
	},
	{
		text: "Sit", weight: 18
	},
	{
		text: "Amet", weight: 19
	},
];

$(function() {
    $("#tagcloud").jQCloud(word_array, {
        width: 450,
        height: 300
    });
});
</script>


<!-- Tag Cloud -->
<div id="tagcloud"></div>




{form_close()}

<ul class="pagination pagination-sm">
  {$set_pagination}
</ul>

</div>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
