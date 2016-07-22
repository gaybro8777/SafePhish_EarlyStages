var app = angular.module("projectsApp", []);
app.controller("projectsController",function($scope) {
    $scope.projects = [];
    $.get("projects/json",function(data) {
        $scope.projects = JSON.parse(data);
        $scope.$apply();
    });
    $scope.filterVar = 'project_name';
});
