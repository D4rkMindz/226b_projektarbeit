<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UpdatedUser extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("users");
        $table->addColumn('password', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'username'])->update();
        $table->addColumn('email', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'password'])->update();
        $this->table("users")->changeColumn('first_name', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'email'])->update();
        $this->table("users")->changeColumn('last_name', 'string', ['null' => false, 'limit' => 255, 'collation' => "latin1_swedish_ci", 'encoding' => "latin1", 'after' => 'first_name'])->update();
        $this->table("users")->changeColumn('login_at', 'datetime', ['null' => false, 'after' => 'last_name'])->update();
        $this->table("users")->changeColumn('active', 'boolean', ['null' => false, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY, 'precision' => 3, 'after' => 'login_at'])->update();
        $table->addColumn('created_at', 'datetime', ['null' => false, 'after' => 'active'])->update();
        $this->table("users")->changeColumn('created_by', 'integer', ['null' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'created_at'])->update();
        $table->addColumn('modified_at', 'datetime', ['null' => true, 'after' => 'created_by'])->update();
        $this->table("users")->changeColumn('modified_by', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR, 'precision' => 10, 'after' => 'modified_at'])->update();
        $this->table("users")->changeColumn('activate_at', 'datetime', ['null' => false, 'default' => '9999-12-31 00:00:00', 'after' => 'modified_by'])->update();
        $table->save();
        if($this->table('users')->hasColumn('created')) {
            $this->table("users")->removeColumn('created')->update();
        }
        if($this->table('users')->hasColumn('modified')) {
            $this->table("users")->removeColumn('modified')->update();
        }
        if($this->table('users')->hasColumn('deleted')) {
            $this->table("users")->removeColumn('deleted')->update();
        }
        if($this->table('users')->hasColumn('deleted_at')) {
            $this->table("users")->removeColumn('deleted_at')->update();
        }
        if($this->table('users')->hasColumn('deleted_by')) {
            $this->table("users")->removeColumn('deleted_by')->update();
        }
    }
}
