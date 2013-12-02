'use strict';

/* App Module */
var BlocNoteApp = angular.module('BlocNoteApp', [
    'ngRoute',
    'BlocNoteAnimations',
    'BlocNoteControllers',
    'BlocNoteFilters',
    'BlocNoteServices'
]);

BlocNoteApp
    .value('appDebug', true)
    .value('urlInterface', 'interface.php')
    .config(['$routeProvider',
    function($routeProvider) {
        $routeProvider
            .when('/notes', {
                templateUrl: 'partials/note-list.html',
                controller: 'BlocNoteReadAllCtrl'
            })
            .when('/note/read/:noteId', {
                templateUrl: 'partials/note-detail.html',
                controller: 'BlocNoteReadNoteCtrl'
            })
            .when('/note/edit/:noteId', {
                templateUrl: 'partials/note-edit.html',
                controller: 'BlocNoteEditNoteCtrl'
            })
            .when('/note/delete/:noteId', {
                templateUrl: 'partials/note-delete.html',
                controller: 'BlocNoteDeleteNoteCtrl'
            })
            .when('/note/create', {
                templateUrl: 'partials/note-edit.html',
                controller: 'BlocNoteCreateNoteCtrl'
            })
            .otherwise({
                redirectTo: '/notes'
            });
    }])
    ;
