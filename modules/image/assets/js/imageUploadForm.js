getApp().

    directive('imageUploadForm', function() {
        return {
            controller: function($scope, $element, $rootScope) {
                $scope.loading = false;
                $scope.error = false;
                $element.ajaxForm({
                    dataType: 'json',
                    beforeSubmit: function() {
                        $scope.loading = true;
                        $scope.error = false;
                        if(!$scope.$$phase) {
                            $scope.$digest();
                        }
                    },
                    success: function(newImages) {
                        $scope.error = false;
                        $rootScope.$emit('imagesUploaded', newImages);
                    },
                    complete: function() {
                        $scope.loading = false;
                        $scope.$digest();
                    },
                    error: function() {
                        $scope.error = true;
                        $scope.$digest();
                    }
                });

                $scope.$on('addImage', function() {
                    $element.find('input').click();
                });

                $scope.upload = function uploadImage() {
                    $element.submit();
                };
            }
        };
    });