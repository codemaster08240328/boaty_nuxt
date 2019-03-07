jQuery(document).ready(function($) {

	$('.mymail-amazonses-api').on('change', function(){

		(parseInt($(this).val(), 10))
			? $('.amazonses-tab-smtp').slideDown()
			: $('.amazonses-tab-smtp').slideUp();

	});

});
