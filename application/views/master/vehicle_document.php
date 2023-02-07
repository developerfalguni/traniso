<?php if (isset($view)) : 

echo start_panel($page_title . ' <small>' . $page_desc . '</small>', '', 'nopadding', '<div class="buttons" id="PageLinks"></div>');
if (strtolower($view['type']) == 'pdf')
	echo '<canvas id="the-canvas"></canvas>';
else
	echo '<img src="' . $view['url'] . '" />';
echo end_panel();

	if (strtolower($view['type']) == 'pdf') : 

?>
<script>
'use strict';
PDFJS.workerSrc = '<?php echo base_url('assets/pdfjs/build/pdf.worker.js') ?>';

PDFJS.getDocument('<?php echo str_replace("'", "\'", $view['url']) ?>').then(function(pdf) {
	var pages = pdf.numPages;
	var i;
	for(i = 1; i <= pages; i++) {
		$('#PageLinks').append('<a href=\"javascript: getPage(' + i + ')\" class=\"btn btn-sm btn-info page_btns\" id=\"page_btn_'+ i +'\">' + i + '</a>&nbsp;');
	}

	pdf.getPage(1).then(function(page) {
		var scale = 1.5;
		var viewport = page.getViewport(scale);
		var canvas = document.getElementById('the-canvas');
		var context = canvas.getContext('2d');
		canvas.height = viewport.height;
		canvas.width = viewport.width;
		var renderContext = {
			canvasContext: context,
			viewport: viewport
		};
		page.render(renderContext);
		$('#Loading').addClass('hide');
	});
});

function getPage(page_no) {
	$('.page_btns').removeClass('btn-primary').addClass('btn-info');
	$('#page_btn_'+page_no).removeClass('btn-info').addClass('btn-primary');
	PDFJS.getDocument('<?php echo str_replace("'", "\'", $view['url']) ?>').then(function(pdf) {
		pdf.getPage(page_no).then(function(page) {
			var scale = 1.5;
			var viewport = page.getViewport(scale);
			var canvas = document.getElementById('the-canvas');
			var context = canvas.getContext('2d');
			canvas.height = viewport.height;
			canvas.width = viewport.width;
			var renderContext = {
				canvasContext: context,
				viewport: viewport
			};
			page.render(renderContext);
		});
	});
}

</script>

<?php 
	endif;
endif;
?>