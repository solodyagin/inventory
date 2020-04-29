<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row-fluid">
		<button id="bdel" class="btn btn-primary">Начать удаление</button>
		<div id="infoblock" class="well" style="display:none"></div>
	</div>
</div>
<script>
	$('#bdel').click(function () {
		$('#infoblock').load('delete/execute', function () {
			$(this).fadeIn();
		});
		return false;
	});
</script>
