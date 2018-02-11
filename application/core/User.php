<?php
/**
 * Created by PhpStorm.
 * User: konstantin
 * Date: 08.02.2018
 * Time: 20:06
 */

class User {
	/**
	 * @var PDO
	 */
	public $pdo;

	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function signUp($name, $login, $password) {
		$stmt = $this->pdo->prepare('SELECT * FROM password WHERE login = :lg');
		$stmt->execute(array(
			':lg' => $login
		));
		if ($stmt->fetch()) {
			return false;
		} else {
			$sql  = 'INSERT INTO user (name, position) VALUES (:n, :pos)';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array(
				':n'   => $name,
				':pos' => 'user'
			));
			$user_id = $this->pdo->lastInsertId();
			$sql     = 'INSERT INTO password (user_id, login, password) VALUES (:ui, :l, :p)';
			$stmt    = $this->pdo->prepare($sql);
			$stmt->execute(array(
				':ui' => $user_id,
				':l'  => $login,
				':p'  => password_hash($password, PASSWORD_DEFAULT)
			));

			return true;
		}
	}

	public function signIn($login, $password) {
		$stmt = $this->pdo->prepare('SELECT user_id FROM password WHERE login = :lg AND password = :pw');
		$stmt->execute(array(
			':lg' => $login,
			':pw' => $password
		));
		if ($stmt->fetch()) {
			return true;
		} else {
			return false;
		}
	}
}