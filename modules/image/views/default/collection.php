<?php
/* @var $this DefaultController */
$this
    ->less('/css/images.less')
    ->script('/js/lib/jquery.form.min.js', CClientScript::POS_END)
    ->script('/js/image/imageItem.js', CClientScript::POS_END)
    ->script('/js/facades/ckEditorFileBrowser.js', CClientScript::POS_END)
    ->script('/js/image/imageUploadForm.js', CClientScript::POS_END)
    ->script('/js/image/imageCollectionCtrl.js', CClientScript::POS_END)
    ->script('/js/image/imageService.js', CClientScript::POS_END);
?>
<div class="col-xs-12">
    <h1>Ваши изображения</h1>


    <?php
        $this->widget('application.modules.image.components.widgets.ImageCollectionWidget');
    ?>
</div>