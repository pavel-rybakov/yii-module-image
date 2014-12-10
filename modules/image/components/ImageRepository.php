<?php

Yii::import('application.modules.image.extensions.EPhpThumb.lib.phpThumb.src.*');

class ImageRepository extends CComponent {

    const FILE_DELIMITER = '/';
    const IMAGES_ASSETS_FOLDER = '_images';

    private $_imageBasePath;
    private $_imageBaseUrl;
    private $_webrootPath;

    public function init() {
        $this->_imageBasePath = Yii::app()->getAssetManager()->getBasePath() . self::FILE_DELIMITER . self::IMAGES_ASSETS_FOLDER;
        $this->_imageBaseUrl = Yii::app()->getAssetManager()->getBaseUrl() . self::FILE_DELIMITER . self::IMAGES_ASSETS_FOLDER;
        $this->_webrootPath = Yii::getPathOfAlias('webroot');
    }

    public function getImages($userId, $count, $index) {

        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id = :user_id and status = :status_active';
        $criteria->params = array(
            ':user_id' => $userId,
            ':status_active' => Image::STATUS_ACTIVE
        );

        return Image::model()->findAll($criteria);
    }

    public function saveThumb(Image $image, $width) {
        /** @var GdThumb $thumb */
        $thumb = Yii::app()->getComponent('phpThumb')
            ->create($this->getImageFilePath($image))
            ->getThumbnail()
            ->resize($width);
        $dimensions = $thumb->getCurrentDimensions();

        $thumbData = $thumb->getImageAsString();

        $thumbInst = new ImageThumbnail();
        $thumbInst->image_id = $image->id;
        $thumbInst->width = $dimensions['width'];
        $thumbInst->height = $dimensions['height'];
        $thumbInst->src = $this->publishImageData($thumbData, $image->original_name, $image->type, $image->user_id, $thumbInst->width, $thumbInst->height);
        if ($thumbInst->save()) {
            return $thumbInst;
        }
        return false;
    }

    public function saveImage(CUploadedFile $uploadedImage) {
        $imageData = file_get_contents($uploadedImage->getTempName());
        $imageSizeInfo = @getimagesize($uploadedImage->getTempName());
        if (!is_array($imageSizeInfo)) {
            return false;
        }
        $imageInst = new Image();
        $imageInst->original_name = $uploadedImage->getName();
        $imageInst->data = $imageData;
        $imageInst->height = $imageSizeInfo[0];
        $imageInst->width = $imageSizeInfo[1];
        $imageInst->user_id = Yii::app()->getUser()->id;
        $imageInst->type = $this->getImageTypeByMimeType($uploadedImage->getType());
        $imageInst->status = Image::STATUS_ACTIVE;
        $imageInst->src = $this->publish($imageInst);
        if ($imageInst->save()) {
            return $imageInst;
        }
        return false;
    }

    public function saveImageFromData($name, $imageData) {
        $imageSizeInfo = @getimagesizefromstring($imageData);
        if (!is_array($imageSizeInfo)) {
            return false;
        }
        $imageInst = new Image();
        $imageInst->original_name = $name;
        $imageInst->data = $imageData;
        $imageInst->height = $imageSizeInfo[0];
        $imageInst->width = $imageSizeInfo[1];
        $imageInst->user_id = Yii::app()->getUser()->id;
        $imageInst->type = IMAGETYPE_PNG;
        $imageInst->status = Image::STATUS_DRAFT;
        $imageInst->src = $this->publish($imageInst);
        if ($imageInst->save()) {
            return $imageInst;
        }
        return false;
    }

    public function publish(Image $image) {
        return $this->publishImageData($image->data, $image->original_name, $image->type, $image->user_id);
    }

    private function publishImageData($imageData, $name, $type, $userId, $width = 0, $height = 0) {
        //если нет папки для пользователя, создаем
        $imageDir = implode(self::FILE_DELIMITER, array($this->_imageBasePath, $userId));
        if (!file_exists($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        //генерим путь для файла
        $hash = self::hash($name . time() . $width . $height);
        $imageFileName = $hash . image_type_to_extension($type, true);
        $imageFilePath = $imageDir . self::FILE_DELIMITER . $imageFileName;

        //сохраняем файл
        if (!file_exists($imageFilePath)) {
            if (!file_put_contents($imageFilePath, $imageData)) {
                return null;
            }
        }

        //возвращаем URI картинки
        $imageUrl = $this->_imageBaseUrl . self::FILE_DELIMITER . $userId . self::FILE_DELIMITER . $imageFileName;
        return $imageUrl;
    }

    private function getImageFilePath(Image $image) {
        return $this->_webrootPath . $image->src;
    }

    private function getThumbFilePath(ImageThumbnail $thumb) {
        return $this->_webrootPath . $thumb->src;
    }

    private function hash($str) {
        return substr(sha1($str), 5, 15);
    }

    private function getImageTypeByMimeType($mimeType) {
        $extensions = array(
            'image/jpeg' => IMAGETYPE_JPEG,
            'image/gif' => IMAGETYPE_GIF,
            'image/pjpeg' => IMAGETYPE_JPEG,
            'image/png' => IMAGETYPE_PNG,
        );
        return $extensions[$mimeType];
    }
}