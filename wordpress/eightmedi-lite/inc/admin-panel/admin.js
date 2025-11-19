jQuery(document).ready(function($){

	//other admin js here.
	var upgrade_notice = '<div class="notice-up">';
    upgrade_notice += '<a class="upgrade-pro-demo" target="_blank" href="http://8degreethemes.com/demos/?theme=eightmedi-pro">View EightMedi PRO</a></div>';
    jQuery('#customize-info').append(upgrade_notice);
    $(document).find('.version-author-info').children('.author').children().attr('target','_blank');
});
