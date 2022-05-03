/* The `idArr`, `urlArr`, and `catArr` variables are defined in the main Yesterlinks `index.html` file */

// $("#base-url").attr("href", "https://links.yesterweb.org/");

var random;

$(function () {
  $("#search-input").val("");
  $("#directory").tablesorter();
  // this unchecks everything when refreshed
  $('input[type="checkbox"]').each(function () {
    $(this).prop("checked", false);
  });
});

$(".order-added-sorting").on("click", "button", function (e) {
  sortOrder = $(event.target).val();
  $("#directory").trigger("sorton", [[[1, sortOrder]]]);
});

$(".rank-sorting").on("click", "button", function (e) {
  sortOrder = $(event.target).val();
  if (sortOrder === "a" || sortOrder === "d") {
    $("#directory").trigger("sorton", [[[4, sortOrder]]]);
    return;
  }

  $(".voting__amount").toggleClass("hide");
});

var firstClick = 0;
var selectedOptions = [];

$("input[type=checkbox]").on("change", function () {
  var inputLength = $('input[type="checkbox"]').length;

  // if it is the first click, we hide all of the rows by default, then show one by one...
  if (firstClick === 0) {
    $("tr").each(function (index) {
      $(this).not(".tablesorter-headerRow").hide();
    });
  }

  // once that runs once, firstClick becomes 1 so it doesn't run again
  firstClick = 1;

  // checks - if nothing is checked, show ALL
  if ($(".filters").find("input:checked").length === 0) {
    console.log("checkboxes full");
    $("tr").each(function (index) {
      $(this).show();
      firstClick = 0;
    });
  }

  var selected = $(this);
  // if a checkbox is checked...
  if ($(this).prop("checked") == true) {
    // add value to array
    selectedOptions.push($(this).val());
    //console.log('checked');

    var checked = $(this).val();

    // loop through each tr element
    $("tr").each(function (index) {
      // grab the matching text from the cat cell
      catText = $(this).children(".cat").text();
      // if the category text matches the text in the table cell
      if (catText === checked) {
        console.log("matched");
        // show it!
        $(this).show();
      }
    });
  } else {
    console.log("unchecked");
    var index = selectedOptions.indexOf(selected);
    selectedOptions.splice(index, 1);
    var unchecked = $(this).val();
    $("tr").each(function (index) {
      // grab the matching text from the cat cell
      catText = $(this).children(".cat").text();
      // if the category text matches the text in the table cell
      if (catText === unchecked) {
        console.log("matched");
        // show it!
        $(this).not(".tablesorter-headerRow").hide();
      }
    });
  }
  console.log(selectedOptions);
});

function updateVoteCount(voteColumn, amount) {
  voteColumn.children(".voting__amount").text(amount);
  voteColumn.attr("data-value", amount);
}

$(".voting").on("click", "button", function () {
  var id = $(event.currentTarget).attr("data-id");
  var isPressed = $(this).attr("aria-pressed");
  var action = $(this).val();

  if (isPressed === "true") {
    action = "remove";
    $(this).attr("aria-pressed", "false");
  } else {
    $(this).attr("aria-pressed", "true");
    $(this).siblings().attr("aria-pressed", "false");
  }

  $.ajax({
    type: "post",
    data: { id: id, action: action },
    url: "submit.php",
    success: function (response) {
      response = JSON.parse(response);
      console.log(response);
      updateVoteCount($("#" + id).children('.voting'), response.count['new']);

      $("#directory").trigger("updateAll", [
        false,
        function () {
          console.log("Table updated.");
        },
      ]);
    },
    error: function (response) {
      console.error(response);
    },
  });
});

$("#search-input").on("keyup", function (e) {
  // value of text field
  var value = $(this).val();
  console.log(e.keyCode);

  // assigns the pattern we're searching for
  var patt = new RegExp(value, "i");
  // in the #directory table, find each tr
  $("#directory")
  .find("tr")
  .each(function () {
    if (value.length === 0) {
      // if search box is empty
      // loop through rows and make highlight 'invisible'.
      console.log("remove Mark");
      //$(this).children('td.descr').children(mark))
      $("mark").css("background-color", "transparent");
      $("mark").css("padding", "0");
    }

    var $table = $(this);

    if (!($table.find("td").text().search(patt) >= 0)) {
      $table.not(".tablesorter-headerRow").hide();
    }

    if ($table.find("td").text().search(patt) >= 0) {
      $(this).show();
      var td = $(this).children("td.desc").children("div.desc");
      var matchedRow = td.text();
      var newMarkup = matchedRow.replace(value, "<mark>" + value + "</mark>");
      td.html(newMarkup);
    }
  });
});

$(".item-tags").on("click", "button", function () {
  $(".tag-filter").css("display", "block");
  console.log($(this).data("tag-id"));
  var tagID = $(this).data("tag-id");

  $("tr").each(function (index) {
    if (
      $(this)
      .children(".desc")
      .children(".item-tags")
      .children("button")
      .data("tag-id") == tagID
    ) {
      console.log("matched tag!");
      //$(this).css('color', 'red');
      // get name of tag
      var myTags = $(this)
      .children(".desc")
      .children(".item-tags")
      .children(".myTags");
      var selectedTag = document.querySelectorAll(
        "[data-tag-id='" + tagID + "']"
      )[0].innerText;
      $("#current-tag").text(selectedTag);
      $(this).show();
    } else {
      $(this).hide();
    }
  });
});

$("#show-all").on("click", function () {
  $("tr").each(function (index) {
    $(".tag-filter").css("display", "none");
    $(this).show();
  });
});
