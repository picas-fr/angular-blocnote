'use strict';

/* Controllers */
var BlocNoteControllers = angular.module('BlocNoteControllers', []);
BlocNoteControllers
    .controller('BlocNoteReadAllCtrl', ['$scope', 'BlocNoteFactory', 'appDebug',
    function($scope, BlocNoteFactory, appDebug) {
        $scope.appDebug = appDebug;
        BlocNoteFactory.query(function(response){
            $scope.notes = objectToArray(response.data);
        })
        ;
        $scope.orderProp = '-age';
    }])
    .controller('BlocNoteReadNoteCtrl', ['$scope', '$routeParams', 'BlocNoteFactory', 'appDebug',
    function($scope, $routeParams, BlocNoteFactory, appDebug) {
        $scope.appDebug = appDebug;
        if ($scope.notes===undefined) {
            BlocNoteFactory.query(function(response){
                $scope.notes = objectToArray(response.data);
            });
        }
        BlocNoteFactory.get({id: $routeParams.noteId}, function(response){
                $scope.note = response.data;
                if ($scope.note.images && $scope.note.images.length) {
                    $scope.mainImageUrl = $scope.note.images[0];
                }
                $scope.setImage = function(imageUrl) {
                    $scope.mainImageUrl = imageUrl;
                }
        })
        ;
    }])
    .controller('BlocNoteCreateNoteCtrl', ['$scope', '$location', '$timeout', 'BlocNoteFactory', 'appDebug',
    function($scope, $location, $timeout, BlocNoteFactory, appDebug) {
        $scope.appDebug = appDebug;
        if ($scope.notes===undefined) {
            BlocNoteFactory.query(function(response){
                $scope.notes = objectToArray(response.data);
            });
        }
        $scope.isClean = function() {
            return false;
        };
        $scope.save = function() {
            BlocNoteFactory.create($scope.note, function(response){
                $scope.status = response.status;
                $scope.messages = response.messages;
                if (response.status=='ok') {
                    $scope.note = response.data;
                    $scope.notes.push(response.data);
                    $timeout(function() { $location.path('/'); });
                }
            })
            ;
        }
    }])
    .controller('BlocNoteEditNoteCtrl', ['$scope', '$location', '$routeParams', 'BlocNoteFactory', 'appDebug',
    function($scope, $location, $routeParams, BlocNoteFactory, appDebug) {
        $scope.appDebug = appDebug;
        var master;
        if ($scope.notes===undefined) {
            BlocNoteFactory.query(function(response){
                $scope.notes = objectToArray(response.data);
            });
        }
        BlocNoteFactory.get({id: $routeParams.noteId}, function(response){
                master = response.data;
                $scope.note = master;
                if ($scope.note.images && $scope.note.images.length) {
                    $scope.mainImageUrl = $scope.note.images[0];
                }
                $scope.setImage = function(imageUrl) {
                    $scope.mainImageUrl = imageUrl;
                }
        });
        $scope.isClean = function() {
            return angular.equals($scope.note, master);
        };
        $scope.destroy = function() {
            $scope.note = null;
            $location.path('/');
        };
        $scope.save = function() {
            BlocNoteFactory.update({id: $routeParams.noteId}, $scope.note, function(response){
                    $scope.status = response.status;
                    $scope.messages = response.messages;
                    if (response.status=='ok') {
                        master = angular.copy($scope.note);
                        var _id = findNoteIndexById($scope.notes, response.data.id);
                        $scope.notes[_id] = response.data;
                        $location.path('/');
                    }
                });
        };
        $scope.reset = function() {
            $scope.note = master;
        };
    }])
    .controller('BlocNoteDeleteNoteCtrl', ['$scope', '$location', '$routeParams', 'BlocNoteFactory', 'appDebug',
    function($scope, $location, $routeParams, BlocNoteFactory, appDebug) {
        $scope.appDebug = appDebug;
        if ($scope.notes===undefined) {
            BlocNoteFactory.query(function(response){
                $scope.notes = objectToArray(response.data);
            });
        }
        var master;
        BlocNoteFactory.get({id: $routeParams.noteId}, function(response){
                master = response.data;
                $scope.note = master;
                if ($scope.note.images && $scope.note.images.length) {
                    $scope.mainImageUrl = $scope.note.images[0];
                }
                $scope.setImage = function(imageUrl) {
                    $scope.mainImageUrl = imageUrl;
                }
        });
        $scope.destroy = function() {
            BlocNoteFactory.delete({id: $routeParams.noteId}, function(response){
                    $scope.status = response.status;
                    $scope.messages = response.messages;
                    if (response.status=='ok') {
                        $location.path('/');
                    }
                });
        };
    }])
    ;
