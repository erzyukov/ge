<div id="banner{$data.id}"></div>
<script>
	var so = new SWFObject('{$data.path}', "banner{$data.id}", "{$data.width}", "{$data.height}", "10", "#FFFFFF");
	so.addParam('wmode', 'transparent');
	so.write("banner{$data.id}");
</script>