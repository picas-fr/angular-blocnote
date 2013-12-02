'use strict';

/* Services */
var BlocNoteServices = angular.module('BlocNoteServices', ['ngResource']);

BlocNoteServices
    .factory('BlocNoteFactory', ['$resource', 'urlInterface',
    function($resource, urlInterface){
        return $resource(urlInterface, {model:'BlocNote'}, {
            query:  {method:'GET'},
            create: {method:'POST'},
            get:    {method:'GET', params: {id: '@noteId'}},
            update: {method:'PUT', params: {id: '@noteId'}},
            delete: {method:'DELETE', params: {id: '@noteId'}}
        });
    }])
    ;
