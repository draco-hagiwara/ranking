<!DOCTYPE html>
<html class="no-js" lang="jp">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>順位チェックツール &#xB7; SEO-RANK</title>

{* Versionと並び順に注意 *}
<link href="{base_url()}../../css/bootstrap.min.css" rel="stylesheet">

{* FontAwesome *}
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<script src="{base_url()}../../js/jquery-2.1.4.min.js"></script>
<script src="{base_url()}../../js/bootstrap.min.js"></script>

<div>
  <section class="container">

{* ヘッダー部分　START *}
  <ul class="list-inline text-right"><li>{$mem_Name}</li></ul>

</head>

{* ヘッダー部分　END *}

<body>

<div id="contents" class="container">

  <div class="form-group">
    <H4><p class="bg-success"><u>地域（ロケーション）リスト</u></p></H4>
  </div>

  <div class="form-horizontal col-sm-10 col-sm-offset-1">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>地域名</th>
          <th>canonical_name</th>
        </tr>
      </thead>

    {foreach from=$list item=lo}
      <tbody>
        <tr>
          <td>
            {$lo.lo_criteria_id}
          </td>
          <td>
            {$lo.lo_name}
          </td>
          <td>
            {$lo.lo_canonical_name}
          </td>
        </tr>
      </tbody>

    {foreachelse}
      地域（ロケーション）情報はありませんでした。
    {/foreach}

    </table>
  </div>

</div>

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
