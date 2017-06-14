<?php

/**
* MainController class
*/ 

include(ROOT .'/models/main.php');

class MainController {
	function actionIndex() {
		$errors = array();
		$id = -1;

		if(isset($_POST['submit'])) {
			$id = intval($_POST['curren_comment']);
			if(!empty($_POST['uname']) && !empty($_POST['mail']) && !empty($_POST['message']) && !empty($_POST['captcha'])) {
				session_start();
				if(mb_strtoupper($_POST['captcha']) == $_SESSION['captcha']) {
					$db = DataBase::getInstance();
					$comment = mysqli_real_escape_string($db, Main::close_tags(strip_tags($_POST['message'], "<strong><b><i><a><code>"))); // удалим левые теги и закроем открытые теги
					
					$parent_id = intval($_POST['parent']);
					$uname = Main::clearPost($_POST['uname']);

					$time = time();

					if(isset($_POST['site'])) {
						if($_POST['site'] == '') $site = 'none';
						else {
							$site = Main::clearPost($_POST['site']);

							if(strlen($site) <= 4 || strlen($site) > 25) {
								array_push($errors, "Длинна сайта от 4 до 25 символов!"); 
							}
						}
					}

					$mail = Main::clearPost($_POST['mail']);
					$ip = $_SERVER['REMOTE_ADDR'];
					$client = $_SERVER['HTTP_USER_AGENT'];

					if(strlen($uname) < 3 || strlen($uname) > 25) {
						array_push($errors, "Длинна имени от 3 до 25 символов!"); 
					}


					if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
						array_push($errors, "Ошибка ввода e-mail!");
					}

					if(count($errors) == 0) {
						mysqli_query($db, "INSERT INTO les_comments (`id`, `name`, `ip`, `client`, `comment`, `parent_id`, `mail`, `time`, `site`) VALUES (NULL, '$uname', '$ip', '$client', '$comment', '$parent_id', '$mail', '$time', '$site')");
					}
				} else { array_push($errors, "Ошибка ввода капчи!"); }
			} else { array_push($errors, "Заполните все необходимые поля!"); }
		} 

		if(isset($_POST['sort'])) $sort = $_POST['sort'];
		else $sort = "date_desc";

		$form = "
		<div class='editor'>
			<span onclick='hide_editor();' style='border-bottom:1px; cursor:pointer; float:right; margin-right:15px;'>x</span>
			<form id='send' method='post'>
				<input type='hidden' name='parent' value='0'>
				<input type='hidden' name='submit' value='1'>
				<input type='hidden' name='curren_comment' value='-1'>
				<input id='uname' name='uname' required type='text' placeholder='Введите Ваше имя' value='' maxlength='20' size='25' />
				<span style='color: red;'>*</span>
				<br>

				<input id='uname' style='margin-top: 0px;' name='mail' required type='text' placeholder='Введите Ваш e-mail' value='' maxlength='20' size='25' />
				<span style='color: red;'>*</span>
				<br>


				<input id='uname' style='margin-top: 0px;' name='site' type='text' placeholder='Введите адрес Вашего сайта' value='' maxlength='20' size='25' />
				<br>

				<div class='editor_area'>
				<div class='add-tag-list' style='width:97.5%; height:20px; background-color:gray;' data-mode-btn-add-tag='b'>
					<button type='button' style='width:25px; cursor:pointer;' onclick='add_tag(1);'>[b]</button> 
					<button type='button' style='width:25px; cursor:pointer;' onclick='add_tag(2);'>[i]</button>  
					<button type='button' style=' cursor:pointer;' onclick='add_tag(3);'>[code]</button> 
					<button type='button' style=' cursor:pointer;' onclick='add_tag(4);'>[a href='']</button> 
				</div>
				<textarea wrap='hard' required class='txt' id='textinput' style='width: 96%;' name='message' placeholder='Введите текст комментария'></textarea>
				</div>
				<br>

				<img style='margin-top: -5px; float: left; border: 1px solid gray; background: url(\"../template/img/bg_capcha.png\");' src='/core/captcha.php' width='120' height='40'/>
				<span style='margin-left: 5px;'>-</span> 
				<input id='captcha' placeholder='Введите код с картинки' name='captcha' type='text' value='' maxlength='6' size='20' />
				<span style='color: red;'>*</span>
				<br><br>

				<input id='submit' onclick='send();' type='button' value='Добавить' />";
			
				if(count($errors) > 0) {
					$form .= "<div class='errors' style='color: red;'><hr>";
					for($i = 0; $i < count($errors); $i++) {
						$form .= "* {$errors[$i]}<br>";
					}
					$form .= "</div><br>";
				}

		$form .= "</form>
		</div>";

		// Get comments from db
		$msg = Main::getCommentsDB($sort);
		$msg = Main::mapTree($msg);

		// Show all comments
		$comments = Main::generateContent($msg, $form, $sort, $id, $form);

		// Show template
		include_once(ROOT .'/views/main_view.php');
	}
}

?>