<?php

class create_image extends CDbMigration {

	public function up() {
        $this->createTable('image', array(
            'id' => 'pk',
            'data' => 'mediumblob not null',
            'user_id' => 'int(11) not null',
            'original_name' => 'text',
            'width' => 'int',
            'height' => 'int',
            'created_at' => 'timestamp not null default current_timestamp',
            'status' => 'int',
            'type' => 'int',
            'src' => 'text',
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');
        $this->addForeignKey('fk_image__user', 'image', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('image_thumbnail', array(
            'id' => 'pk',
            'image_id' => 'int(11) not null',
            'width' => 'int',
            'height' => 'int',
            'src' => 'text'
        ), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');
        $this->addForeignKey('fk_image_thumbnail__image', 'image_thumbnail', 'image_id', 'image', 'id', 'CASCADE', 'CASCADE');
	}

	public function down() {
        $this->dropTable('image_thumbnail');
        $this->dropTable('image');
        return true;
	}
}