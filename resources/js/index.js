function showInsights(resp) {
  $container = $('<div>')
  $pageStats = `
    <h3>Speed Score: ${resp.ruleGroups.SPEED.score}</h3>
    <table class="Section__table">
      <tbody>
        <tr>
          <td class="Section__table-cell">Number of Resources</td><td class="Section__table-cell">${resp.pageStats.numberResources}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">Number of Hosts</td><td class="Section__table-cell">${resp.pageStats.numberHosts}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">Total Request Bytes</td><td class="Section__table-cell">${resp.pageStats.totalRequestBytes}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">Number Static Resources</td><td class="Section__table-cell">${resp.pageStats.numberStaticResources}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">HTML Response Bytes</td><td class="Section__table-cell">${resp.pageStats.htmlResponseBytes}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">CSS Response Bytes</td><td class="Section__table-cell">${resp.pageStats.cssResponseBytes}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">Image Response Bytes</td><td class="Section__table-cell">${resp.pageStats.imageResponseBytes}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">JavaScript Response Bytes</td><td class="Section__table-cell">${resp.pageStats.javascriptResponseBytes}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">Number of JS Resources</td><td class="Section__table-cell">${resp.pageStats.numberJsResources}</td>
        </tr>
        <tr>
          <td class="Section__table-cell">Number of CSS Resources</td><td class="Section__table-cell">${resp.pageStats.numberCssResources}</td>
        </tr>
      </tbody>
    </table>
  `
  $container.append($pageStats)
  $('[data-insights]').empty()
  $('[data-insights]').append($container)
}

$(document).ready(function() {
  if ($('[data-key]')) {
    var key = $('[data-key]').data('key')
    var siteUrl = $('[data-url]').data('url')

    $('[data-toggle]').on('click', function(e) {
      $(e.target)
        .next()
        .toggleClass('visible')
    })

    if (key) {
      $.ajax({
        method: 'GET',
        url: "https://www.googleapis.com/pagespeedonline/v2/runPagespeed",
        dataType: 'json',
        data: {
          key: key,
          url: siteUrl,
          fields: "pageStats,ruleGroups",
          strategy: 'desktop'
        },
        success: resp => {
          showInsights(resp)
        },
        error: resp => {
          $('[data-insights]').empty()
          var errors = resp.responseJSON.error.errors

          for (var i = 0; i < errors.length; i++) {
            $('[data-insights]').append(`<p class="Section__error">${errors[i].message} (${errors[i].reason})</p>`)
          }
        }
      })
    }
  }
})
