<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class Init extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER DATABASE CHARACTER SET 'latin1';");
        $this->execute("ALTER DATABASE COLLATE='latin1_swedish_ci';");
        $table = $this->table("user_roles", ['engine' => "InnoDB", 'encoding' => "latin1", 'collation' => "latin1_swedish_ci", 'comment' => ""]);
        $table->save();
        if ($this->table('user_roles')->hasColumn('id')) {
            $this->table("user_roles")->changeColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->update();
        } else {
            $this->table("user_roles")->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->update();
        }
        $table->addColumn('name', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'id'])->update();
        $table->addColumn('level', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'name'])->update();
        $table->save();
        $table = $this->table("users", ['engine' => "InnoDB", 'encoding' => "latin1", 'collation' => "latin1_swedish_ci", 'comment' => ""]);
        $table->save();
        if ($this->table('users')->hasColumn('id')) {
            $this->table("users")->changeColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->update();
        } else {
            $this->table("users")->addColumn('id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'identity' => 'enable'])->update();
        }
        $table->addColumn('user_role_id', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'id'])->update();
        $table->addColumn('username', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'user_role_id'])->update();
        $table->addColumn('first_name', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'username'])->update();
        $table->addColumn('last_name', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'first_name'])->update();
        $table->addColumn('login_at', 'datetime', ['null' => false, 'after' => 'last_name'])->update();
        $table->addColumn('active', 'boolean', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'login_at'])->update();
        $table->addColumn('created', 'datetime', ['null' => false, 'after' => 'active'])->update();
        $table->addColumn('created_by', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'created'])->update();
        $table->addColumn('modified', 'datetime', ['null' => true, 'after' => 'created_by'])->update();
        $table->addColumn('modified_by', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'modified'])->update();
        $table->addColumn('deleted', 'boolean', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'modified_by'])->update();
        $table->addColumn('deleted_at', 'datetime', ['null' => true, 'after' => 'deleted'])->update();
        $table->addColumn('deleted_by', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'deleted_at'])->update();
        $table->addColumn('activate_at', 'datetime', ['null' => false, 'default' => '9999-12-31 00:00:00', 'after' => 'deleted_by'])->update();
        $table->save();
    }
}
