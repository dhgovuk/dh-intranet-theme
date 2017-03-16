/* global jQuery, location */

var enquire = require('./plugins/enquire')
var equalheight = require('./plugins/equalheight.js')
require('./plugins/jquery-accessibleMegaMenu')

jQuery(function ($) {
  jQuery(function () {
    enquire.register('screen and (min-width:500px)', { // jshint ignore:line
      match: function () {
        // Main Nav
        $('.menu-main-container').accessibleMegaMenu({
          uuidPrefix: 'accessible-nav',
          menuClass: 'nav-menu',
          topNavItemClass: 'nav-item',
          panelClass: 'sub-nav',
          panelGroupClass: 'sub-nav-group',
          hoverClass: 'hover',
          focusClass: 'focus',
          openClass: 'open'
        })
        // hack so that the megamenu doesn't show flash of css animation after the page loads.
        setTimeout(function () {
          $('body').removeClass('init')
        }, 500)
      },
      unmatch: function () {}
    })
  })

  // Extend jQuery to make a toggle text function.
  jQuery.fn.extend({
    toggleText: function (stateOne, stateTwo) {
      return this.each(function () {
        stateTwo = stateTwo || ''
        $(this).text() !== stateTwo && stateOne ? $(this).text(stateTwo) : $(this).text(stateOne)
      })
    }
  })

  // Navigation Toggle
  jQuery(function () {
    $('#js-navigation-toggle').click(function () {
      $(this).toggleText('Close', 'Menu')
      $('#js-navigation').toggleClass('opened')
    })
  })

  // Super simple tabs
  jQuery(function () {
    var tabs = $('.tab')
    tabs.hide().filter(':first').attr('aria-hidden', 'true').show()
    var tabsnav = $('.campaign-tabs-navigation li a')

    tabsnav.click(function () {
      tabs.hide().attr('aria-hidden', 'true')
      tabs.filter(this.hash).attr('aria-hidden', 'false').show()
      tabsnav.removeClass('selected').attr('aria-expanded', 'false')
      $(this).addClass('selected').attr('aria-expanded', 'true')
      return false
    }).filter(':first').click().attr('aria-expanded', 'true')
  })

  // More Panel
  jQuery(function () {
    $('.js-more-panel').addClass('hide')
    $('.policy-kit-article').each(function () {
      var policyarticle = $(this)
      var panelToggle = $(this).find('.js-more-panel-toggle')
      var panel = $(this).find('.js-more-panel')
      panel.addClass('hide').attr('aria-hidden', 'true')
      policyarticle.removeClass('open')

      panelToggle.click(function (event) {
        var siblings = policyarticle.siblings()
        var destination = $(this).parent()
        siblings.find('.js-more-panel').addClass('hide')
        siblings.removeClass('open')
        policyarticle.toggleClass('open')
        panel.toggleClass('hide')
        event.preventDefault()
        $('html, body').animate({
          scrollTop: $(destination).offset().top - 30
        }, 300)
      })
    })

    $('.policy-kit-article').each(function () {
      var tabs = $(this).find('.tab')
      tabs.hide().filter(':first').attr('aria-hidden', 'true').show()
      var tabsnav = $(this).find('.policy-tabs-navigation li a')

      tabsnav.click(function () {
        tabs.hide().attr('aria-hidden', 'true')
        tabs.filter(this.hash).attr('aria-hidden', 'false').show()
        // console.log(this.hash)
        tabsnav.removeClass('selected').attr('aria-expanded', 'false')
        $(this).addClass('selected').attr('aria-expanded', 'true')
        return false
      }).filter(':first').click().attr('aria-expanded', 'true')
    })
  })

  /*
   * Policy Dashboard
   */

  $(document).ready(function () {
    // Get phase to reopen.
    var cookieData = getCookie('PTK-phase')
    if (cookieData !== '') {
      $('#policy-stage-select').val(cookieData)
      showSteps()
    }

    // Get stepIDs to reopen.
    cookieData = getCookie('PTK-openstep')
    if (cookieData !== '') {
      var stepsArray = cookieData === '' ? [] : JSON.parse(cookieData)
      for (var i = stepsArray.length - 1; i >= 0; i--) {
        expandStep(stepsArray[i])
      }
    }

    // Remember if recommended steps were showen
    cookieData = getCookie('PTK-recommended')
    if (cookieData !== '') {
      if (cookieData === 'yes') {
        toggleShowRecommendedSteps()
      }
    }
  })

  /*
   * User clicks the show steps button
   */
  $('#showsteps').click(function (event) {
    // Save in a cookie so we can restore the view between sessions
    setCookie('PTK-phase', $('#policy-stage-select').val(), 24 * 30) // 30 days.
    showSteps()
    return false
  })

  /*
   * Show the steps depending on the value of the dropdown
   */
  function showSteps () {
    // hide all boxes
    $('.policy-step').hide()
    // show the one selected in the dropdown
    $('.policy-step.' + $('#policy-stage-select').val() + '-must-do').fadeToggle(200)

    // if the show recommended had been clicked before then do show them
    if ($('#show-recommended-steps').data('clicked')) {
      $('.policy-step.' + $('#policy-stage-select').val() + '-recommended').slideToggle(200)
    }
    // suppress default behaviour.
    return false
  }

  /*
   * Respond to user clicking a policy step to expand it
   */
  $('.init-step-box').click(function () {
    var stepID = $(this).attr('data')
    var cookieData = getCookie('PTK-openstep')
    var stepsArray = cookieData === '' ? [] : JSON.parse(cookieData)
    if ($('.' + stepID).data('clicked')) {
      // Remove all instance of this step id from the array
      // NOTE: forEach not supported by IE8
      for (var i = stepsArray.length - 1; i >= 0; i--) {
        if (stepsArray[i] === stepID) stepsArray.splice(i, 1)
      }
    } else {
      stepsArray.push(stepID)
    }
    setCookie('PTK-openstep', JSON.stringify(stepsArray), 24 * 30) // 30 days.
    expandStep(stepID)
  })

  /*
   * Called from user click or from the state being restored from cookie data.
   */
  function expandStep (stepID) {
    $('.' + stepID).next('.expanded-step-box').slideToggle(100)
    // update the glyph
    if ($('.' + stepID).data('clicked')) {
      $('.' + stepID).find('.policy-step-more').html('More')
    } else {
      $('.' + stepID).find('.policy-step-more').html('Less')
    }
    // keep track of if the div is expanded or not.
    $('.' + stepID).data('clicked', !$('.' + stepID).data('clicked'))
  }

  /*
   * Show the recommended step and store the users preference in a cookie.
   */
  $('#show-recommended-steps').click(function () {
    if ($('#show-recommended-steps').data('clicked')) {
      setCookie('PTK-recommended', 'no', -1)
    } else {
      setCookie('PTK-recommended', 'yes', 24 * 30) // 30 days.
    }
    toggleShowRecommendedSteps()
  })

  /*
   * Expand the recommend step section, either in responce to user input or value stored in cookie.
   */
  function toggleShowRecommendedSteps () {
    $('.policy-step.' + $('#policy-stage-select').val() + '-recommended').slideToggle(200)

    if ($('#show-recommended-steps').data('clicked')) {
      $('#show-recommended-steps').text('Show recommended steps')
    } else {
      $('#show-recommended-steps').text('Hide recommended steps')
    }
    $('#show-recommended-steps').data('clicked', !$('#show-recommended-steps').data('clicked'))
  }

  // make modalboxs cature tab input
  $('#lastInput').blur(function () {
    $('#firstInput').focus()
  })

  /**
   * Cookie functions
   * note - set all paths to root.
   */
  function setCookie (cname, cvalue, exhours) {
    var d = new Date()
    d.setTime(d.getTime() + (exhours * 60 * 60 * 1000))
    var expires = 'expires=' + d.toGMTString()
    document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/'
  }
  function getCookie (cname) {
    var name = cname + '='
    var ca = document.cookie.split(';')
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i]
      while (c.charAt(0) === ' ') c = c.substring(1)
      if (c.indexOf(name) !== -1) return c.substring(name.length, c.length)
    }
    return ''
  }

  // expand to show casestudy on policy article page
  $('.show-casestudy-button').click(function () {
    $(this).next('.show-casestudy-box').slideToggle(100)
    // rip .toggle depeciated in jq1.9
    $(this).data('clicked', !$(this).data('clicked'))
    if ($(this).data('clicked')) {
      $(this).text('Hide case study')
    } else {
      $(this).text('Show case study')
    }
  })

  /**
   * Admin bar
   */
  $('body').has('#wpadminbar').addClass('adminbar')

  /**
   * Setting user_meta on location select box change
   */
  $('[data-location-selector]').change(function () {
    var $this = $(this)
    var action = $this.attr('data-action')
    var nonce = $this.attr('data-nonce')
    var val = parseInt($this.val(), 10)

    $.ajax({
      url: action,
      method: 'POST',
      data: {
        action: 'select-location',
        _wpnonce: nonce,
        location: val
      },
      success: function (data) {
        var json = JSON.parse(data)

        if (json.ok) {
          location.reload(true)
        } else {
          location.replace('?news_locale=' + val)
        }
      },
      error: function () {}
    })
  })

  /**
   * show any emergency messages that haven't been dismissed
   */
  var eMsgCookie
  var postCookieName
  $('.emergency-message').each(function () {
    postCookieName = $(this).attr('data-post-cookie')
    eMsgCookie = getCookie(postCookieName)
    if (eMsgCookie !== 'true') {
      $(this).css('display', 'block')
    }
  })

  // Exclude news
  $('.js-exclude-news').click(function (e) {
    if (typeof window.ga !== 'undefined') {
      var action = this.checked ? 'CheckExcludeNewsCheckbox' : action = 'UncheckExcludeNewsCheckbox'
      window.ga('send', 'event', 'Search', action)
    }
    this.form.submit()
  })

  // Equal heights for event items
  $(window).resize(function () {
    equalheight('.event-item')
  })
})
