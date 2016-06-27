/* global jQuery, confirm */

jQuery('body').delegate('.up', 'click', function (event) {
  event.preventDefault()

  if (jQuery(this).parent().parent().prev().attr('class') === 'status-container') {
    var above = jQuery(this).parent().parent().prev().contents()
    var current = jQuery(this).parent().parent().contents()

    jQuery(this).parent().parent().append(above)
    jQuery(this).parent().parent().prev().append(current)
  }
})

jQuery('body').delegate('.down', 'click', function (event) {
  event.preventDefault()

  if (jQuery(this).parent().parent().next().attr('class') === 'status-container') {
    var current = jQuery(this).parent().parent().contents()
    var below = jQuery(this).parent().parent().next().contents()

    jQuery(this).parent().parent().append(below)
    jQuery(this).parent().parent().next().append(current)
  }
})

jQuery('body.toplevel_page_it-updates').delegate('.delete', 'click', function (event) {
  event.preventDefault()
  if (confirm('Are you sure you want to delete this? You will need to press the "Save Changes" button.')) {
    jQuery(this).parent().parent().remove()
  } else {
    return false
  }
})

var statusContainerCount = jQuery('.status-container').length

jQuery('.add-new-status').click(function (event) {
  statusContainerCount++

  jQuery(
    '<tr class="status-container" position="' + statusContainerCount + '">' +
    '<td class="text"><input type="text" size="35" name="statuses[' + statusContainerCount + '][system_name]" value="IT System ' + statusContainerCount + '"></td>' +
    '<td class="green"><input type="radio" name="statuses[' + statusContainerCount + '][status]" value="green" checked=""></td>' +
    '<td class="amber"><input type="radio" name="statuses[' + statusContainerCount + '][status]" value="amber"></td>' +
    '<td class="red"><input type="radio" name="statuses[' + statusContainerCount + '][status]" value="red"></td>' +
    '<td><a class="up" href="#" alt="Move up">Move up</a></td>' +
    '<td><a class="down" href="#" alt="Move down">Move down</a></td>' +
    '<td><a class="delete" href="#" alt="Delete">Delete</a></td>' +
    '</tr>').insertBefore('.overall-status')
})
