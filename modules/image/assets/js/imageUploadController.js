getApp().
    controller('imageUploadController', function($scope, imageService, $rootScope) {

        $scope.image = null;

        $scope.init = function(image) {
            if (image) {
                $scope.image = image;
            }
        };

        $scope.addImage = function() {
            $scope.$emit('addImage');
        };

        $scope.cancel = function() {
            imageService
                .delete($scope.image['image_id'])
                .success(function() {})
                .error(function() {
                    console.error('Something goes wrong');
                });
            $scope.image = null;
        };

        $rootScope.$on('imagesUploaded', function(e, newImages) {
            $scope.image = newImages[0];
            $rootScope.image = $scope.image;
            $scope.$digest();
        });

        $scope.$on('deleteImage', function(e, imageId) {
            if (confirm('Delete?')) {
                imageService
                    .delete(imageId)
                    .success(function() {
                        removeImage();
                    })
                    .error(function() {
                        alert('Something goes wrong');
                    });
            }
        });

        var removeImage = function() {
            $scope.image = null;
        };
    });