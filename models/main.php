<?php

/**
* Comments model class
*/
class Main {
	public static function getCommentsDB($sort) {
		$msg = array();

		switch ($sort) {
			case 'date_desc':
				$sort = 'ORDER BY time DESC';
				break;

			case 'date_asc':
				$sort = 'ORDER BY time ASC';
				break;
			
			case 'name_desc':
				$sort = 'ORDER BY name DESC';
				break;

			case 'name_asc':
				$sort = 'ORDER BY name ASC';
				break;

			case 'mail_desc':
				$sort = 'ORDER BY mail DESC';
				break;

			case 'mail_asc':
				$sort = 'ORDER BY mail ASC';
				break;
		}

		$db = DataBase::getInstance();
		$result = mysqli_query($db, "SELECT * FROM comments {$sort}");
		while($row = mysqli_fetch_assoc($result)) {
			$msg[$row['id']] = $row;
		}
		return $msg;
	}

	public static function generateComment($w, $p_id, $form) {
		$w['comment'] = nl2br(htmlspecialchars_decode($w['comment']));
		$date = date("d.m.Y в H:i", $w['time']);

		if($w['parent_id'] == 0) $margin = 0;
		else $margin = 20;

		$comments = "<li id='comment{$w['id']}' style='margin-left: {$margin}px'><div class='comment-title'><span style='float:left'><b>{$w['name']}</b><small> ({$date})</small></span><span class='comment-ans' id={$w['id']}>Ответить</span></div><div id='comment_msg_{$w['id']}' class='comment-message'>{$w['comment']}</div>";

		if($p_id == $w['id']) {
			$comments .= $form;
		}

		if(isset($w['childs'])) {
			$comments .= "<ul id='commentsRoot{$w['id']}'>";
			$comments .= Main::makeAllComments($w['childs'], $p_id, $form);
			$comments .= "</ul>"; 
		}
		$comments .= "</li>";
		return $comments;
	}

	public static function makeAllComments($data, $p_id, $form) {
		$comments = '';
		foreach ($data as $w) {
			$comments .= Main::generateComment($w, $p_id, $form);
		}
		return $comments;  
	}

	public static function generateContent($msg, $form, $sort, $p_id, $form) {
		if(count($msg)) {
			$comments = "
			<div style='width:100%; height:30px;'></div>
			<div class='holder' style='display:inline;'></div>
			<form method='post' action='/' style='margin-bottom:5px; display:inline; float:right;'>
				<select id='sort' name='sort' onChange='this.form.submit();'>";

				$comments .= $sort == 'date_desc' ? '<option value=date_desc selected>По убыванию даты</option>' : '<option value=date_desc>По убыванию даты</option>'; 

				$comments .= $sort == 'date_asc' ? '<option value=date_asc selected>По возростанию даты</option>' : '<option value=date_asc>По возростанию даты</option>'; 

				$comments .= $sort == 'name_desc' ? '<option value=name_desc selected>По убыванию имени (Z-A)</option>' : '<option value=name_desc>По убыванию имени (Z-A)</option>'; 

				$comments .= $sort == 'name_asc' ? '<option value=name_asc selected>По возростанию имени (A-Z)</option>' : '<option value=name_asc>По возростанию имени (A-Z)</option>'; 

				$comments .= $sort == 'mail_desc' ? '<option value=mail_desc selected>По убыванию e-mail (Z-A)</option>' : '<option value=mail_desc>По убыванию e-mail (Z-A)</option>'; 

				$comments .= $sort == 'mail_asc' ? '<option value=mail_asc selected>По возростанию e-mail (A-Z)</option>' : '<option value=mail_asc>По возростанию e-mail (A-Z)</option>'; 

			$comments .= "</select>
			</form>
			<div style='width:100%; height:5px;'></div>
			<div class='comments-all'><span style='float:left'>Всего комментариев: ". count($msg) ."</span><span class='add-comment'>Написать комментарий</span></div>";

			if($p_id == -1) { $comments .= $form; }

			$comments .= "<ul id='commentRoot'>";

			$comments .= Main::makeAllComments($msg, $p_id, $form);

			$comments .= "</ul>";
		} else {
			$comments = "<div class='comments-all' style='margin-top: 30px;'><span style='float:left'>Ещё нету ни одного комментария</span><span class='add-comment'>Написать комментарий</span></div>". $form;
		}
		return $comments;
	}

	public static function mapTree($dataset) {
		$tree = array(); // Создаем новый массив

		foreach ($dataset as $id=>&$node) {    
			if (!$node['parent_id']) { // не имеет родителя, т.е. корневой элемент
				$tree[$id] = &$node;
			} else { 
			    /*
	             Иначе это чей-то потомок
	             этого потомка переносим в родительский элемент, 
	             при этом у родителя внутри элемента создастся массив childs, в котором и будут вложены его потомки
	            */
	            $dataset[$node['parent_id']]['childs'][$id] = &$node; 
			}
		}
		return $tree;
	}	

	public static function close_tags($content)
    {
        $position = 0;
        $open_tags = array();
        //теги для игнорирования
        $ignored_tags = array('br', 'hr', 'img');
 
        while (($position = strpos($content, '<', $position)) !== FALSE) {
            //забираем все теги из контента
            if (preg_match("|^<(/?)([a-z\d]+)\b[^>]*>|i", substr($content, $position), $match)) {
                $tag = strtolower($match[2]);
                //игнорируем все одиночные теги
                if (in_array($tag, $ignored_tags) == FALSE) {
                    //тег открыт
                    if (isset($match[1]) AND $match[1] == '') {
                        if (isset($open_tags[$tag]))
                            $open_tags[$tag]++;
                        else
                            $open_tags[$tag] = 1;
                    }

                    //тег закрыт
                    if (isset($match[1]) AND $match[1] == '/') {
                        if (isset($open_tags[$tag]))
                            $open_tags[$tag]--;
                    }
                }
                $position += strlen($match[0]);
            }
            else $position++;
        }

        //закрываем все теги
        foreach ($open_tags as $tag => $count_not_closed) {
            $content .= str_repeat("</{$tag}>", $count_not_closed);
        }
        return $content;
    }

    public static function clearPost($data) {
    	$data = trim($data); // Удаляем пробелы
    	$data = stripslashes($data); // очистка экранированных символов
    	$data = strip_tags($data); // очистка тэгов
    	$data = htmlspecialchars($data); // переобразуем символы в сущности

    	return $data;
    }
}