angular.module('soraps', []).filter('nl2br', [
  '$sce', function($sce) {
    return function(input) {
      if (input) {
        return $sce.trustAsHtml(input.replace(/(\r\n|\r|\n)/g, '<br>'));
      }
    };
  }
]).filter('trusted', [
  '$sce', function($sce) {
    return function(input) {
      return $sce.trustAsResourceUrl(input);
    };
  }
]);
