<?php

/**
 * This is the model class for table "{{image}}".
 *
 * The followings are the available columns in table '{{image}}':
 * @property integer
 * @property string $data
 * @property integer $user_id
 * @property string $original_name
 * @property integer $width
 * @property integer $height
 * @property string $created_at
 * @property integer $status
 * @property integer $type
 * @property string $src
 *
 * The followings are the available model relations:
 * @property User $user
 * @property ImageThumbnail[] $imageThumbnails
 */
class Image extends BaseModel {

    const STATUS_ACTIVE = 0;
    const STATUS_DRAFT = 1;
    const STATUS_DELETED = 2;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Image the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'image';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {

		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('data, user_id', 'required'),
			array('user_id, width, height, status, type', 'numerical', 'integerOnly'=>true),
			array('original_name, src', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, data, user_id, original_name, width, height, created_at, status, type, src', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {

		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
			'imageThumbnails' => array(self::HAS_MANY, 'ImageThumbnail', 'image_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {

		return array(
			'id' => 'ID',
			'data' => 'Data',
			'user_id' => 'User',
			'original_name' => 'Original Name',
			'width' => 'Width',
			'height' => 'Height',
			'created_at' => 'Created At',
			'status' => 'Status',
			'type' => 'Type',
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
		$criteria->compare('data',$this->data,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('original_name',$this->original_name,true);
		$criteria->compare('width',$this->width);
		$criteria->compare('height',$this->height);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('type',$this->type);
		$criteria->compare('src',$this->src,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getThumb($width = 150) {
        foreach ($this->imageThumbnails as $thumb) {
            if (intval($thumb->width, 10) === $width) {
                return $thumb;
            }
        }
        return Yii::app()->imageRepo()->saveThumb($this, $width);
    }

    public function mine() {
        return $this->user_id === Yii::app()->user->id;
    }

    public function deleteAvailable() {
        return $this->mine() || Yii::app()->isAdmin();
    }

    public function beforeDelete() {
        /*Удалять картинку из ФС нельзя, она могла использоваться в какой-нибудь статье.
        Поэтому просто изменяем статус картинки на DELETED и не показываем ее в списке картинок. Файл не трогаем.*/
        if (!$this->deleteAvailable()) {
            return false;
        }
        $this->status = self::STATUS_DELETED;
        $this->save();
        return false;
    }

    public function recover() {
        if ($this->mine() || Yii::app()->isAdmin()) {
            $this->status = self::STATUS_ACTIVE;
            $this->save();
            return true;
        }
        return false;
    }
}