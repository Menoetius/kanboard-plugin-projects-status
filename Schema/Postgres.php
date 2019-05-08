<?php


namespace Kanboard\Plugin\Status\Schema;

const VERSION = 1;

function version_1($pdo)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS recovery_plan (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        owner_id INTEGER NOT NULL,
        user_modified INTEGER,
        date INTEGER NOT NULL,
        last_modified INTEGER,
        accomplished TEXT,
        plan TEXT,
        is_active BOOLEAN DEFAULT '1',
        deleted BOOLEAN DEFAULT '0',
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS issue (
        id SERIAL PRIMARY KEY,
        recovery_plan_id INTEGER NOT NULL,
        project_id INTEGER NOT NULL,
        task_id INTEGER,
        user_assignee INTEGER,
        user_issued INTEGER,
        name VARCHAR(255),
        description TEXT,
        due_date INTEGER,
        date_creation INTEGER NOT NULL,
        status INTEGER DEFAULT 0 NOT NULL,
        position SMALLINT DEFAULT 1,
        priority SMALLINT,
        deleted BOOLEAN DEFAULT '0',
        FOREIGN KEY(recovery_plan_id) REFERENCES recovery_plan(id) ON DELETE CASCADE,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY(user_assignee) REFERENCES users(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS issue_steps (
        id SERIAL PRIMARY KEY,
        recovery_plan_id INTEGER NOT NULL,
        issue_id INTEGER NOT NULL,
        owner_id INTEGER NOT NULL,
        date_creation INTEGER NOT NULL,
        text TEXT,
        deleted BOOLEAN DEFAULT '0',
        FOREIGN KEY(recovery_plan_id) REFERENCES recovery_plan(id) ON DELETE CASCADE,
        FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(issue_id) REFERENCES issue(id) ON DELETE CASCADE
    )");

    $pdo->exec("ALTER TABLE projects ADD COLUMN project_status INTEGER DEFAULT '0'");
}