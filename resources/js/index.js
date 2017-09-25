$(document).ready(function(){
  $('[data-toggle]').on('click', function(e) {
    $(e.target).next().toggleClass('visible')
  })
})
