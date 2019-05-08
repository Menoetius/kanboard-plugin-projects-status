<?php

namespace Kanboard\Plugin\Status\Schema;


const VERSION = 1;

function version_1($pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS recovery_plan (
        id INT NOT NULL AUTO_INCREMENT,
        project_id INT NOT NULL,
        owner_id INT NOT NULL,
        user_modified INT,
        date INT NOT NULL,
        last_modified INT,
        accomplished TEXT,
        plan TEXT,
        is_active TINYINT DEFAULT 1,
        deleted TINYINT DEFAULT 0,
        PRIMARY KEY (id),
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec('CREATE TABLE IF NOT EXISTS issue (
        id INT PRIMARY KEY,
        recovery_plan_id INT NOT NULL,
        project_id INT NOT NULL,
        task_id INT,
        user_assignee INT,
        user_issued INT,
        name VARCHAR(255),
        description TEXT,
        due_date INT,
        date_creation INT NOT NULL,
        status TINYINT DEFAULT 0 NOT NULL,
        position INT DEFAULT 1,
        priority TINYINT,
        deleted TINYINT DEFAULT 0,
        PRIMARY KEY (id),
        FOREIGN KEY(recovery_plan_id) REFERENCES recovery_plan(id) ON DELETE CASCADE,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY(user_assignee) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8
    ');

    $pdo->exec('CREATE TABLE IF NOT EXISTS issue_steps (
        id INT PRIMARY KEY,
        recovery_plan_id INT NOT NULL,
        issue_id INT NOT NULL,
        owner_id INT NOT NULL,
        date_creation INT NOT NULL,
        text TEXT,
        deleted TINYINT DEFAULT 0,
        PRIMARY KEY (id),
        FOREIGN KEY(recovery_plan_id) REFERENCES recovery_plan(id) ON DELETE CASCADE,
        FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(issue_id) REFERENCES issue(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8
    ');

    $pdo->exec("ALTER TABLE projects ADD COLUMN project_status INT DEFAULT '0'");
}
