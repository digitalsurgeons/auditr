function displayRecommendation(recommendation, index) {
  if (recommendation.summary) {
    let summary = recommendation.summary

    if (summary.args) {
      summary.args.forEach(function(arg) {
        // populate format string
        if (arg.type == 'HYPERLINK') {
          summary.format = summary.format.replace(
            '{{BEGIN_LINK}}',
            `<a href="${arg.value}">&nbsp;`
          )
          summary.format = summary.format.replace('{{END_LINK}}', `</a>`)
        } else {
          summary.format = summary.format.replace(`{{${arg.key}}}`, arg.value)
        }
      })
    }

    return `
      <dt class="Section__list-title">
        <img class="Section__list-icon" src="/${window.location.pathname.split(
          '/'
        )[1]}/resources/auditr/img/${recommendation.ruleImpact > 0
      ? 'cross'
      : 'check'}-icon.jpg" />
        ${recommendation.localizedRuleName}
      </dt>
      <dd class="Section__list-item">
        ${recommendation.summary.format}
      </dd>
    `
  }
}

function showInsights(resp) {
  // show score
  let rating
  if (resp.ruleGroups.SPEED.score < 65) {
    rating = 'poor'
  } else if (resp.ruleGroups.SPEED.score < 85) {
    rating = 'needsWork'
  } else {
    rating = 'good'
  }

  $('[data-insights]').empty()
  $('[data-insights]').append(`
      <div class="Speed-score Speed-score--${rating}">
        <p class="Speed-score__score">${resp.ruleGroups.SPEED.score} / 100</p>
      </div>
    `)

  // dynamically make the score circular
  $('.Speed-score__score').height($('.Speed-score__score').width())
  $('.Speed-score__score').css(
    'line-height',
    $('.Speed-score__score').width() + 'px'
  )

  // initialize lists
  // is there a better way to initialize multiple variables to the same value
  // without having them reference each other?
  let recommendations = $('<dl>', {
    class: 'Section__list Section__list--defs-list'
  })

  let optimizationsFound = $('<dl>', {
    class: 'Section__list Section__list--defs-list'
  })

  let stats = $('<dl>', {
    class: 'Section__list Section__list--defs'
  })

  // create recommendations lists
  let rules = resp.formattedResults.ruleResults

  let filters = [
    {
      // if the rule impact is 0, this is something you've already done
      f: rule =>
        rules[rule].summary != undefined && rules[rule].ruleImpact === 0.0,
      $o: optimizationsFound
    },
    {
      f: rule =>
        rules[rule].summary != undefined && rules[rule].ruleImpact > 0.0,
      $o: recommendations
    }
  ]

  filters.forEach(function(filter) {
    $(filter.$o).append(
      Object.keys(rules)
        .filter(filter.f)
        .map(function(key, index) {
          return displayRecommendation(rules[key], index)
        })
    )
  })

  // $('[data-insights]').append('<h3 class="Section__subheader">Opimizations Found</h3>')
  $('[data-insights]').append(optimizationsFound)

  // $('[data-insights]').append('<h3 class="Section__subheader">Recommendations</h3>')
  $('[data-insights]').append(recommendations)

  Object.keys(resp.pageStats).forEach(function(key, index) {
    // camelCase => Title Case
    let title = key
      .replace(/([A-Z])/g, ' $1')
      .replace(/^./, str => str.toUpperCase())

    $(stats).append(`
        <dt class="Section__list-title${index % 2 == 0
          ? ' Section__list-item--gray'
          : ''}">${title}</dt>
        <dd class="Section__list-item${index % 2 == 0
          ? ' Section__list-item--gray'
          : ''}">${resp.pageStats[key]}</dd>
      `)
  })

  $('[data-insights]').append('<h3 class="Section__subheader">Statistics</h3>')
  $('[data-insights]').append(stats)
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
        url: 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed',
        dataType: 'json',
        data: {
          key: key,
          url: siteUrl,
          fields: 'pageStats,formattedResults,ruleGroups',
          strategy: 'desktop'
        },
        success: resp => {
          showInsights(resp)
        },
        error: resp => {
          $('[data-insights]').empty()
          var errors = resp.responseJSON.error.errors

          for (var i = 0; i < errors.length; i++) {
            $('[data-insights]').append(`
                <p class="Section__error">
                  Error: ${errors[i].message} (${errors[i].reason})
                </p>
              `)
          }
        }
      })
    }
  }
})
