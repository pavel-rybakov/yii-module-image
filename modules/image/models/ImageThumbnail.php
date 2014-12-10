<?php

/**
 * This is the model class for table "{{image_thumbnail}}".
 *
 * The followings are the available columns in table '{{image_thumbnail}}':
 * @property integer $id
 * @property integer $image_id
 * @property integer $width
 * @property integer $height
 * @property string $src
 *
 * The followings are the available model relations:
 * @property Image $image
 */
class ImageThumbnail extends BaseModel {

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ImageThumbnail the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'image_thumbnail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('image_id', 'required'),
			array('image_id, width, height', 'numerical', 'integerOnly'=>true),
			array('src', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, image_id, width, height, src', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'image' => array(self::BELONGS_TO, 'Image', 'image_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {

		return array(
			'id' => 'ID',
			'image_id' => 'Image',
			'width' => 'Width',
			'height' => 'Height',
			'src' => 'Src',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('image_id',$this->image_id);
		$criteria->compare('width',$this->width);
		$criteria->compare('height',$this->height);
		$criteria->compare('src',$this->src,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}