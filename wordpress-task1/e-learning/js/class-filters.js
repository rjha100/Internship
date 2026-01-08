jQuery(document).ready(function ($) {
  $(".search-bar-form").on("submit", function (e) {
    var subject = $('select[name="subject"]', ".filter-sidebar-form").val();
    var level = $('select[name="level"]', ".filter-sidebar-form").val();
    var city = $('input[name="city"]', ".filter-sidebar-form").val();
    var priceMin = $('input[name="price_min"]', ".filter-sidebar-form").val();
    var priceMax = $('input[name="price_max"]', ".filter-sidebar-form").val();
    var isMember = $('input[name="is_member"]', ".filter-sidebar-form").is(
      ":checked",
    )
      ? "1"
      : "";
    var dateFrom = $('input[name="date_from"]', ".filter-sidebar-form").val();
    var dateTo = $('input[name="date_to"]', ".filter-sidebar-form").val();
    var timeFrom = $('input[name="time_from"]', ".filter-sidebar-form").val();
    var timeTo = $('input[name="time_to"]', ".filter-sidebar-form").val();

    // Update hidden inputs in search form
    $('input[name="subject"]', this).val(subject);
    $('input[name="level"]', this).val(level);
    $('input[name="city"]', this).val(city);
    $('input[name="price_min"]', this).val(priceMin);
    $('input[name="price_max"]', this).val(priceMax);
    $('input[name="is_member"]', this).val(isMember);
    $('input[name="date_from"]', this).val(dateFrom);
    $('input[name="date_to"]', this).val(dateTo);
    $('input[name="time_from"]', this).val(timeFrom);
    $('input[name="time_to"]', this).val(timeTo);
  });

  $(".filter-sidebar-form").on("submit", function (e) {
    var searchValue = $('input[name="class_search"]', ".search-bar-form").val();

    $('input[name="class_search"]', this).val(searchValue);
  });

  var filterToggle = $("#filter-toggle");
  var filterPanel = $("#filter-panel");

  filterToggle.on("click", function () {
    filterPanel.toggleClass("filter-panel-open");

    if (filterPanel.hasClass("filter-panel-open")) {
      $(this).find("span:not(.filter-badge)").text("Hide Filters");
    } else {
      $(this).find("span:not(.filter-badge)").text("Filters");
    }
  });


  $(".filter-tag").on("click", function () {});

  // Reset number inputs if they contain invalid values
  $('input[type="number"]').on("blur", function () {
    var val = $(this).val();
    if (val !== "" && (isNaN(val) || parseFloat(val) < 0)) {
      $(this).val("");
    }
  });

  // Validate price range
  $('input[name="price_max"]').on("blur", function () {
    var min = parseFloat($('input[name="price_min"]').val()) || 0;
    var max = parseFloat($(this).val());

    if (max && max < min) {
      alert("Maximum price cannot be less than minimum price.");
      $(this).val("");
    }
  });

  // Validate date range
  $('input[name="date_to"]').on("blur", function () {
    var from = $('input[name="date_from"]').val();
    var to = $(this).val();

    if (from && to && new Date(to) < new Date(from)) {
      alert("End date cannot be before start date.");
      $(this).val("");
    }
  });

  // Validate time range
  $('input[name="time_to"]').on("blur", function () {
    var from = $('input[name="time_from"]').val();
    var to = $(this).val();

    if (from && to && to < from) {
      alert("End time cannot be before start time.");
      $(this).val("");
    }
  });
});
