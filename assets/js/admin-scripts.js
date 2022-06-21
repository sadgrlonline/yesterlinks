var random;

$(function () {
  $("#directory").tablesorter();
});

$('td').on("click", ".edit", function (e) {
  e.preventDefault();

  $(this).attr("aria-pressed", "true");

  var url = $(this).parent('td').siblings('.urlAdmin').text();

  // to remove
  //var cat = $(this).parent('td').parent('.row').children('.cat').text();

  var title = $(this).parent('td').parent('.row').children('.title').text();
  var descr = $(this).parent('td').parent('.row').children('.descr').text();
  var id = $(this).attr("id");

  console.log(id);

  $(this).parent('td').parent('.row').children('.urlAdmin').append('<input type="text" class="titleInput"></input>');
  $(this).parent('td').parent('.row').children('.title').html('<input type="text" class="titleInput" value="' + title + '"></input>');
  $(this).parent('td').parent('.row').children('.urlAdmin').html('<input type="text" class="urlInput" value="' + url + '"></input>');
  $(this).parent('td').parent('.row').children('.descr').html('<textarea rows="15" class="descrInput">' + descr + '</textarea>');

  // to remove
  //$(this).parent('td').parent('.row').children('.cat').html('<input type="text" class="catInput" value="' + cat + '"></input>');

  $(this).parent('td').siblings('.tags').children('ul').addClass('hide');
  $(this).parent('td').parent('.row').children('.tags').children('.tags__edit').removeClass('hide');


  $('td').on("click", ".save", function (e) {
    e.preventDefault();

    $(this).siblings(".edit").attr("aria-pressed", "false");

    var id = $(this).attr('data-id');
    var url = $(this).parent('td').siblings('.urlAdmin').children('.urlInput').val();

    // to remove
    //var cat = $(this).parent('td').parent('.row').children('.cat').children('.catInput').val();

    var title = $(this).parent('td').parent('.row').children('.title').children('.titleInput').val();
    var descr = $(this).parent('td').parent('.row').children('.descr').children('.descrInput').val();
    var tags = $(this).parent('td').parent('.row').children('.tags').find('input[type="checkbox"]:checked');
    var tagList = $(this).parent('td').parent('.row').children('.tags').children('ul');

    var chosenTagIDs = [];
    tagList.empty(); // clear the list of tags on the page

    /* Loop through the chosen tags and make an array of their IDs to send to `submit.php` */
    for (var i = 0; i < tags.length; i++) {
      chosenTagIDs.push(tags[i].value);

      tagList.append(`<li>${$(tags[i]).siblings('label').text()}</li>`); // Add this tag value to the list in the tags column
    }
    $(this).parent('td').siblings('.urlAdmin').html('<a href="' + url + '">' + url + '</a>');

    // remove
    //$(this).parent('td').siblings('.cat').text(cat);
    $(this).parent('td').siblings('.title').text(title);
    $(this).parent('td').siblings('.descr').text(descr);
    $(this).parent('td').siblings('.tags').children('ul').removeClass('hide');
    $(this).parent('td').siblings('.tags').children('.tags__edit').addClass('hide');
    //console.log(id, title, url, descr, cat, tags);
    $.ajax({
      type: 'post',
      data: {
        'id': id,
        'title': title,
        'url': url,
        'descr': descr,
        // remove
        //'cat':cat,
        'tags': JSON.stringify(chosenTagIDs)
      },
      url: '../submit.php',
      success: function (response) {
        //location.reload();
      }
    });
  });
});

$('.del').on("click", function (e) {
  e.preventDefault();
  var id = $(this).attr('data-id');
  var del = "del";
  if (confirm("Are you sure you want to delete this entry?") === true) {
    $(this).parent('td').parent('.row').remove();
    $.ajax({
      type: 'post',
      data: {
        'id': id,
        'del': del
      },
      url: '../submit.php',
      success: function (response) {
        //location.reload();
      }
    });
  }
});

$('.approve').on("change", "input", function () {
  var id = $(this).attr('data-id');
  var pending = parseInt($(this).val());
  console.log(id, pending, $(this).is(":checked"));

  if ($(this).is(":checked") === true) {
    $(this).val(1);
  }

  if ($(this).is(":checked") === false) {
    $(this).val(0);
  }

  $.ajax({
    type: 'post',
    data: {
      'approved': pending,
      'id': id
    },
    url: '../submit.php',
    success: function (response) {


    }
  });
});

$('#searchInput').on('keyup', function (e) {
  // value of text field
  var value = $(this).val();
  console.log(e.keyCode);

  // assigns the pattern we're searching for
  var patt = new RegExp(value, "i");
  // in the #directory table, find each tr
  $('#directory').find('tr').each(function () {
    if (value.length === 0) {
      // if search box is empty
      // loop through rows and make highlight 'invisible'.
      console.log('remove Mark');
      //$(this).children('td.descr').children(mark))
      $('mark').css('background-color', 'transparent');
      $('mark').css('padding', '0');

    }

    var $table = $(this);

    if (!($table.find('td').text().search(patt) >= 0)) {
      $table.not('.tablesorter-headerRow').hide();
    }

    if (($table.find('td').text().search(patt) >= 0)) {
      $(this).show();
      var td = $(this).children('td.desc').children('div.desc');
      var matchedRow = td.text();
      var newMarkup = matchedRow.replace(value, '<mark>' + value + '</mark>');
      td.html(newMarkup);

    }
  });
});