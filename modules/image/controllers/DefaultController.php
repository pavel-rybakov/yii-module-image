<?php

class DefaultController extends BaseController {

	public function actionIndex() {
        $this->layout='//layouts/simple';

        $user = Yii::app()->getUser();
        if ($user->getIsGuest()) {
            return;
        }
        $userImages = $user->model()->images;

        $this->render('collection', array(
            'images' => $userImages,
        ));
	}

    public function actionGet() {
        $request = Yii::app()->getRequest();
        if (!$request->isAjaxRequest || Yii::app()->user->getIsGuest()) {
            throw new CHttpException(403);
        }
        $count = $request->getParam('count', 0);
        $index = $request->getParam('index', 0);

        /** @var Image[] $images */
        $images = Yii::app()->imageRepo()->getImages(Yii::app()->user->id, $count, $index);
        $result = array();
        foreach($images as $image) {
            $result[] = $image->getThumb();
        }

        echo CJSON::encode($result);
        Yii::app()->end();
    }

    public function actionUpload() {
        $result = array();
        /** @var CUploadedFile[] $images */
        $images = CUploadedFile::getInstancesByName('image');
        foreach($images as $image) {
            $savedImage = Yii::app()->imageRepo()->saveImage($image);
            if ($savedImage) {
                $result[] = $savedImage->getThumb(800);
            }
        }
        echo CJSON::encode($result);
        Yii::app()->end();
    }

    public function actionDelete() {
        $imageId = Yii::app()->getRequest()->getParam('image_id');
        /** @var Image $image */
        $image = Image::model()->findByPk(intval($imageId, 10));
        if (!$image) {
            return false;
        }
        $image->delete();
        echo "deleted";
        Yii::app()->end();
    }

    public function actionRecover() {
        $imageId = Yii::app()->getRequest()->getParam('image_id');
        /** @var Image $image */
        $image = Image::model()->findByPk(intval($imageId, 10));
        if (!$image) {
            return false;
        }
        if ($image->recover()) {
            echo "recovered";
        } else {
            throw new CHttpException(403);
        }

        Yii::app()->end();
    }

    public function actionThumb() {
        $request = Yii::app()->getRequest();
        $imageId = $request->getParam('image_id');
        $width = intval($request->getParam('width', 0), 10);
        /** @var Image $image */
        $image = Image::model()->findByPk(intval($imageId, 10));

        //проверяем, что картинка принадлежит текущему пользователю
        if (!$image || $image->user_id !== Yii::app()->user->id) {
            return false;
        }
        if ($width === 0) {
            $imageAttributes = $image->attributes;
            //не отдаем blob
            unset($imageAttributes['data']);
            echo CJSON::encode($imageAttributes);
        } else {
            echo CJSON::encode($image->getThumb($width));
        }
        Yii::app()->end();
    }

    public function actionSiteScreenShot($uri) {
        $screenData = $this->getScreenShotImage($uri);
        if (!empty($screenData)) {
            $savedImage = Yii::app()->imageRepo()->saveImageFromData($uri, $screenData);
            echo CJSON::encode($savedImage->getThumb());
        }
        Yii::app()->end();
    }

    public function getScreenShotImage($url) {
        $defaultWidth = 400;
        $defaultHeight = 300;
        $screen = 1280;
        $screenShotUriTemplate = "http://api.webthumbnail.org/?width={width}&height={height}&screen={screen}&format=png&url={url}";
        $screenShotUri = strtr($screenShotUriTemplate, array(
            '{width}' => $defaultWidth,
            '{height}' => $defaultHeight,
            '{screen}' => $screen,
            '{url}' => $url
        ));

        return @file_get_contents($screenShotUri);
    }
}