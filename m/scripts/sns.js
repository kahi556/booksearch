/**
 * sns.js
 *
 * @version  0.1.0
 * @url 
 *
 * �eSNS�ւ̃{�^���ݒu�p
 *
 */

$(function(){
	// SNS�{�^��
	$('#facebook_like').socialbutton('facebook_like', {
		button: 'button_count'
	});
	$('#twitter').socialbutton('twitter',{
		button: 'none',
		text: '�c�C�[�g����',
		lang: 'ja',
		related: 'twitter'
	});
	$('#evernote').socialbutton('evernote', {
		button: 'article-clipper-jp',
		styling: 'full'
	});
	$('#hatena').socialbutton('hatena');
	$('#gree').socialbutton('gree_sf', {
		button: 0
	});
});
