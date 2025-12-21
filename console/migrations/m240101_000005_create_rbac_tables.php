<?php

use yii\db\Migration;

/**
 * Migration for RBAC tables
 * 
 * Creates tables for Role-Based Access Control:
 * - auth_items: roles and permissions
 * - auth_item_children: role hierarchy
 * - auth_assignments: user-role assignments
 * - auth_rules: custom rules
 */
class m240101_000005_create_rbac_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Auth Rules Table
        $this->createTable('{{%auth_rules}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        // Auth Items Table (roles and permissions)
        $this->createTable('{{%auth_items}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_items_rule_name',
            '{{%auth_items}}',
            'rule_name',
            '{{%auth_rules}}',
            'name',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('idx_auth_items_type', '{{%auth_items}}', 'type');

        // Auth Item Children Table (role hierarchy)
        $this->createTable('{{%auth_item_children}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_item_children_parent',
            '{{%auth_item_children}}',
            'parent',
            '{{%auth_items}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_auth_item_children_child',
            '{{%auth_item_children}}',
            'child',
            '{{%auth_items}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        // Auth Assignments Table (user-role assignments)
        $this->createTable('{{%auth_assignments}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_assignments_item_name',
            '{{%auth_assignments}}',
            'item_name',
            '{{%auth_items}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx_auth_assignments_user_id', '{{%auth_assignments}}', 'user_id');

        echo "    > RBAC tables created successfully.\n";
        echo "    > Run 'yii rbac/init' to initialize roles and permissions.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auth_assignments}}');
        $this->dropTable('{{%auth_item_children}}');
        $this->dropTable('{{%auth_items}}');
        $this->dropTable('{{%auth_rules}}');
    }
}
