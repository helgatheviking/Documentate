jQuery(document).ready(function($) {
	var kbe = $('#live-search #s').val();
	$('#live-search #s').liveSearch({url: '<?php echo home_url(); ?>/?ajax=on&post_type=documentate&s='});

	$('#s').keyup(function() {
		$('#search-result').slideDown("slow");
	});

	$('body').click(function(){
		$('#search-result').slideDown("slow",function(){
			document.body.addEventListener('click', boxCloser, false);
		});
	});
		
	function boxCloser(e){
		if(e.target.id != 's'){
			document.body.removeEventListener('click', boxCloser, false);
			$('#search-result').slideUp("slow");
		}
	}

	var tree_id = 0;
	$('div.documentate_category:has(.documentate_child_category)').addClass('has-child').prepend('<span class="switch dashicons dashicons-search"></span>').each(function () {
		tree_id++;
		$(this).attr('id', 'tree' + tree_id);
	});

	$('div.documentate_category > span.switch').click(function () {
		var tree_id = $(this).parent().attr('id');
		if ($(this).hasClass('open')) {
			$(this).parent().find('div:first').slideUp('fast');
			$(this).removeClass('open');
			$(this).html('<img src="<?php echo get_stylesheet_directory_uri() ?>/documentate_images/documentate_icon-plus.png" />');
		} else {
			$(this).parent().find('div:first').slideDown('fast');
			$(this).html('<img src="<?php echo get_stylesheet_directory_uri() ?>/documentate_images/documentate_icon-minus.png" />');
			$(this).addClass('open');
		}
	});  
 });