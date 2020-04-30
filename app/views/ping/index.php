<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="well">
		<input id="test_ping" class="btn btn-primary" name="test_ping" value="Проверить">
		<div id="ping_add"></div>
	</div>
</div>
<script>
	$('#test_ping').click(function () {
		$('#ping_add').html('<img src="public/img/loading.gif">');
		$('#ping_add').load('route/deprecated/server/common/ping.php?orgid=' + defaultorgid);
	});
</script>
