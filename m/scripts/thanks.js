/**
 * thanks.js
 *
 * @version  0.1.0
 * @url 
 *
 * �eThanks!�����N�ݒu�p
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
