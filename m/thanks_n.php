<?php
//
// �uThanks!���������v�����N�N���b�N
//

session_start();

// ���O�C����Ԃ̃`�F�b�N
if (!isset($_SESSION['login'])) {
	header("Location: login.php");
	exit;
}

// �ϐ�������
$p_brno = "";
$p_user_id = "";

//***********************************************
// ��M�f�[�^�����Ƃɕϐ��̐ݒ� POST
//***********************************************
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	// ���ꕶ����HTML�G���e�B�e�B�ɕϊ��i�Z�L�����e�B�΍�j
	if(isset($_POST["book_review_no"])){$p_brno=htmlspecialchars($_POST["book_review_no"]);}
	if(isset($_POST["user_id"])){$p_user_id=htmlspecialchars($_POST["user_id"]);}
	// �����̃G�X�P�[�v�i�Z�L�����e�B�΍�j
	$p_brno=preg_replace("/;/"," ",addslashes($p_brno));
	$p_user_id=preg_replace("/;/"," ",addslashes($p_user_id));
}

//***********************************************
// DB�ڑ�
//***********************************************
include 'common/database.php';
$obj = new comdb();

//***********************************************
// �g�����U�N�V�����J�n
//***********************************************
$obj->BeginTran();

//***********************************************
// �T���N�X�����X�V �폜�t���O�𗧂Ă�
//***********************************************
$sql = "UPDATE fg_thanks_history_table SET";
$sql.= " delete_flg = 1";
$sql.= " WHERE book_review_no = \"".$p_brno."\"";
$sql.= " AND user_id = \"".$_SESSION["user_id"]."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_thanks_history_table]\n";
}
//***********************************************
// ���]�X�V�i�T���N�X�j���Z
//***********************************************
$sql = "UPDATE fg_book_review_table SET";
$sql.= " thanks_cnt = thanks_cnt - 1";
$sql.= " WHERE book_review_no = \"".$p_brno."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_book_review_table]\n";
}
//***********************************************
// ���[�U�[�X�V�i�T���N�X�j���Z
//***********************************************
$sql = "UPDATE fg_user_table SET";
$sql.= " thanks_cnt = thanks_cnt - 1";
$sql.= " WHERE user_id = \"".$p_user_id."\"";
$ret = $obj->Execute($sql);
if (!$ret){
	$err = true;
	$msg_info.= ERR_UPD."[fg_user_table]\n";
}

if ($err) {
	$obj->RollBack();
	echo $msg_info."�X�V�Ɏ��s���܂���";
}else{
	$obj->Commit();
	echo "<a href=\"javascript:cThanks_y('".$p_brno."','".$p_user_id."')\">Thanks!</a>";
}

?>