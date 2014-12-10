<div ng-controller='imageCollectionCtrl' ng-init="init()" class="image-collection">
    <div>
        <a class="btn btn-success" href="javascript: void 0;" ng-click="addImage()">+ Добавить</a>
    </div>

    <form image-upload-form action="/image/default/upload" method="post" enctype="multipart/form-data">
        <div ng-show="loading">
            <img src="/images/loader2.gif" />
        </div>
        <div ng-show="error">
            Используйте изображения размером менее 2мб
        </div>
        <input ng-show="false" type="file" name="image[]" multiple onchange="angular.element(this).scope().upload(this)"/><br/>
    </form>

    <div ng-show="!!selectedImage" class="selected-image">
        <div>
            <strong>Выбрана фотография</strong>
            <img ng-src="{{selectedImage.src}}" />
        </div>
        <strong>Какой размер?</strong>
        <div ng-repeat="size in sizes">
            <a href="javascript: void 0;" ng-click="useSize(size.width)">{{size.name}} <small ng-show="size.width > 0">ширина {{size.width}}px</small></a>
        </div>
        <a ng-click="useMySize = true" href="javascript: void 0;">Другой</a>
        <input type="number" ng-show="useMySize" ng-model="mySize"/>
        <a ng-click="useSize(mySize)" ng-show="useMySize" href="javascript: void 0;">Готово</a>
    </div>

    <div ng-repeat="image in images" class="image-item">
        <div image-item image="image">
            <div ng-show="!image.deleted">
                <img ng-src="{{ image.src }}" ng-click="select()"/>
                <small ng-click="delete()" title="удалить">удалить</small>
            </div>
            <div ng-show="image.deleted">
                <span>Фотография удалена</span>
                <small ng-click="recover()">восстановить</small>
            </div>
        </div>
    </div>
</div>