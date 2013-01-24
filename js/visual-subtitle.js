jQuery(function ($) {
	wptitlehint('visual-subtitle');
	$('#the-list').on( 'click', 'a.editinline', function() {
		var id = inlineEditPost.getId(this);
		$('#post_subtitle').val($('#inline_' + id + '_visual_subtitle').text());
	});
});

