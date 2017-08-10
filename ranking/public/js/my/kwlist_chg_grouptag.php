<!--
 * Group ＆ Tag設定 script
 *
 * HTML と Script 混在
 *
-->
  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-md-9">
      <select multiple="multiple" name="kw_group[]" id="select2group" style="width: 500px;">
        {$options_group}
      </select>
    </div>
  </div>

<script type="text/javascript">
$(function() {
  $("#select2group").select2({
	tags: true,
	maximumSelectionLength: 1,
  });
});
</script>

  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      <select multiple="multiple" name="kw_tag[]" id="select2tag" style="width: 500px;">
        {$options_tag}
      </select>
    </div>
  </div>

<script type="text/javascript">
$(function() {
  $("#select2tag").select2({
	tags: true,
  });
});
</script>
