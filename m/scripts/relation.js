/**
 * relation.js
 *
 * @version  0.1.0
 * @url 
 *
 * �ւ�胊���N�ݒu�p
 *
 */

// �uThanks!�v�����N�N���b�N
function cThanks_y(book_review_no,user_id){
        $.ajax({
               type: 'POST',
               url: 'thanks_y.php',
               data: "book_review_no="+book_review_no+"&user_id="+user_id,
               success: function(data) {
                   var tag_name = '#thanks'+book_review_no;
                   $(tag_name).html(data);
               },
               error:function() {
                   alert('��肪����܂����B');
               }
        });
}
// �uThanks!���������v�����N�N���b�N
function cThanks_n(book_review_no,user_id){
        $.ajax({
               type: 'POST',
               url: 'thanks_n.php',
               data: "book_review_no="+book_review_no+"&user_id="+user_id,
               success: function(data) {
                   var tag_name = '#thanks'+book_review_no;
                   $(tag_name).html(data);
               },
               error:function() {
                   alert('��肪����܂����B');
               }
        });
}
// �u�t�H���[�v�����N�N���b�N
function cFollow_y(follow_user_id){
        $.ajax({
               type: 'POST',
               url: 'follow_y.php',
               data: "follow_user_id="+follow_user_id,
               success: function(data) {
                   var tag_name = '#follow';
                   $(tag_name).html(data);
                   window.location.reload();
               },
               error:function() {
                   alert('��肪����܂����B');
               }
        });
}
// �u�t�H���[���������v�����N�N���b�N
function cFollow_n(follow_user_id){
        $.ajax({
               type: 'POST',
               url: 'follow_n.php',
               data: "follow_user_id="+follow_user_id,
               success: function(data) {
                   var tag_name = '#follow';
                   $(tag_name).html(data);
                   window.location.reload();
               },
               error:function() {
                   alert('��肪����܂����B');
               }
        });
}
