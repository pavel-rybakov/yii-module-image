var __myApp;

function getApp() {

    if (__myApp) {
        return __myApp;
    }
    __myApp = angular.module('myApp', ['ui.utils', 'ngCookies']);

    return __myApp;
}
getApp();