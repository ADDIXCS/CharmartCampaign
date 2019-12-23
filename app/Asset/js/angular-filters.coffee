angular.module(
  'soraps'
  []
).filter('nl2br', ['$sce', ($sce) ->
  (input) ->
    if input
      return $sce.trustAsHtml input.replace /(\r\n|\r|\n)/g, '<br>'
]).filter('trusted', ['$sce', ($sce) ->
  (input) ->
    return $sce.trustAsResourceUrl input
])

