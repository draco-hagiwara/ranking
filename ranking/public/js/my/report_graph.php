<!--
 * グラフ表示script
 *
 * jsでデータを上手く受けられない？
 *
-->
<script>
new Morris.Line({
	  // ID of the element in which to draw the chart.
	  element: 'rankchart',
	  // Chart data records -- each entry in this array corresponds to a point on
	  // the chart.
	  data: {$graph_data},
	  // The name of the data record attribute that contains x-values.
	  xkey: 'date',
	  // A list of names of data record attributes that contain y-values.
	  ykeys: ['rank'],
	  // Labels for the ykeys -- will be displayed when you hover over the
	  // chart.
	  labels: ['Rank'],
	  //ymin: 300,
	  //ymax: 0,
	  ymin: 301,
	  ymax: 1,
	  xLabels: 'decade',
	  hideHover: true,
	  smooth: false,
	  parseTime: true,
	  postUnits: "位"
	});
</script>