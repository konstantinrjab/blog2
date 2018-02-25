<?php
/**
 * Created by PhpStorm.
 * User: konstantin
 * Date: 24.01.2018
 * Time: 17:28
 */

class Route {
	static function start(PDO $pdo) {
		// контроллер и действие по умолчанию
		$controller_name = 'Main';
		$action_name     = 'index';

		$routes = explode('/', $_SERVER['REQUEST_URI']);

		// получаем имя контроллера
		if ( !empty($routes[1])) {
			$controller_name = $routes[1];
		}

		// получаем имя экшена
		if ( !empty($routes[2])) {
			$action_name = $routes[2];
		}

		// добавляем префиксы
		$model_name      = 'Model_'.$controller_name;
		$controller_name = 'Controller_'.$controller_name;
		$action_name     = 'action_'.$action_name;

		// подцепляем файл с классом модели (файла модели может и не быть)

		$model_file = strtolower($model_name).'.php';
		$model_path = "application/models/".$model_file;
		if (file_exists($model_path)) {
			include "application/models/".$model_file;
		}

		// подцепляем файл с классом контроллера
		$controller_file = strtolower($controller_name).'.php';
		$controller_path = "application/controllers/".$controller_file;
		if (file_exists($controller_path)) {
			include "application/controllers/".$controller_file;
		} else {
			/*
			правильно было бы кинуть здесь исключение,
			но для упрощения сразу сделаем редирект на страницу 404
			*/
			Route::ErrorPage404();
		}

		// создаем контроллер
		$controller = new $controller_name($pdo);
		$action     = $action_name;

		if (method_exists($controller, $action)) {
			// вызываем действие контроллера
			$controller->$action();
		} //поставили лайк
		else if (method_exists($controller, 'getArticle') && empty($_POST['article_id'])) //
		{
			print_r($_POST);

//			echo 'call getArticle';
			$id = preg_replace('/[^0-9]/', '', $action);
			$controller->getArticle($id);

		} elseif ($_POST['article_id']) {
			require_once('like.php');
		} else {
			Route::ErrorPage404();
		}

	}

	function ErrorPage404() {
		$host = 'http://'.$_SERVER['HTTP_HOST'].'/';
		header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		header('Location:'.$host.'404');
	}
}
