<?php declare(strict_types=1);

namespace Migrations;

use DB\SQL;

final class Migration01
{
	private $db;

	public function __construct(SQL $db)
	{
		$this->db = $db;
	}

	public function migrate(): void
	{
		$this->db->exec(
			'CREATE TABLE users (
				id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(255) NOT NULL,
				email VARCHAR(255) NOT NULL UNIQUE,
				password VARCHAR(255) NOT NULL
			);
			
			CREATE TABLE projects (
				id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				title TEXT NOT NULL,
				uploaded_text TEXT NOT NULL,
				source_language TEXT NOT NULL,
				target_language TEXT NOT NULL,
				segments TEXT NOT NULL,
				glossary TEXT,
				creation_date DATETIME NOT NULL,
				last_saved DATETIME NOT NULL,
				user_id INT NOT NULL,
				CONSTRAINT fk_user_constraint
				FOREIGN KEY fk_user(user_id)
				REFERENCES users(id)
				ON DELETE cascade
				ON UPDATE cascade
			);'
		);
	}
}
