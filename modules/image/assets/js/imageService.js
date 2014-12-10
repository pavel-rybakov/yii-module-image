getApp().
    value('IMAGE_METHODS',
    {
        get: '/image/default/get?count={count}&index={index}',
        delete: '/image/default/delete?image_id={image_id}',
        recover: '/image/default/recover?image_id={image_id}',
        thumb: '/image/default/thumb?image_id={image_id}&width={width}',
        upload: '/image/default/index#{cb}',
        saveSiteScreenShot: '/image/default/siteScreenShot?uri={site_uri}'
    }).

    factory('imageService', function($http, IMAGE_METHODS) {
        return {
            delete: function(imageId) {
                var deleteImageUrl = IMAGE_METHODS.delete.
                    replace('{image_id}', imageId);

                return $http.get(deleteImageUrl);
            },
            getThumbnail: function(imageId, width) {
                var thumbnailUrl = IMAGE_METHODS.thumb.
                    replace('{image_id}', imageId).
                    replace('{width}', width);

                return $http.get(thumbnailUrl);
            },
            getSiteScreenShot: function(siteUri) {
                var saveSiteScreenShotUrl = IMAGE_METHODS.
                    saveSiteScreenShot.
                    replace('{site_uri}', encodeURIComponent(siteUri) );

                return $http.get(saveSiteScreenShotUrl);
            }
        }
    });