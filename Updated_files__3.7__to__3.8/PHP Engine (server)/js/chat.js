window.App || ( window.App = {} );

App.hChatTimer = 0;
App.chat_time_ms = 2000;

App.chatInit = function(chat_id, user_id, access_token) {

    if (App.hChatTimer) clearTimeout(App.hChatTimer);
    App.chatRun(chat_id, user_id, access_token);
};

App.chatRun = function(chat_id, user_id, access_token) {

    if (typeof options.pageId !== typeof undefined && options.pageId === "chat") {

        Messages.update(chat_id, user_id, access_token)
    }
};


window.Messages || ( window.Messages = {} );

Messages.update = function (chat_id, user_id, access_token) {

  var message_id = $("li.message-item").last().attr("data-id");

  $.ajax({
    type: 'POST',
    url: '/ajax/chatUpdate.php',
    data: 'access_token=' + access_token + "&chat_id=" + chat_id + "&user_id=" + user_id + "&message_id=" + message_id,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

      if (response.hasOwnProperty('html')) {

        $("ul.content-list").append(response.html);
      }

      if (response.hasOwnProperty('items_all')) {

        items_all = response.items_all;
        items_loaded = $('li.message-item').length;
      }

      App.chat_time_ms = App.chat_time_ms + 1000;

      App.hChatTimer = setTimeout(function() {

        App.chatInit(chat_id, user_id, access_token);

      }, App.chat_time_ms);
    },
    error: function(xhr, status, error) {

      //var err = eval("(" + xhr.responseText + ")");
      //alert(err.Message);
    }
  });
};

Messages.create = function (chat_id, user_id, access_token) {


  var message_text = $('input[name=message_text]').val();
  var message_img = $('input[name=message_image]').val();
  var message_id = $("li.message-item").last().attr("data-id");

  $.ajax({
    type: 'POST',
    url: '/ajax/msg.php',
    data: 'message_text=' + encodeURIComponent(message_text) + '&message_img=' + message_img + '&access_token=' + access_token + "&chat_id=" + chat_id + "&user_id=" + user_id + "&message_id=" + message_id,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

        if (response.hasOwnProperty('promode')) {

            var url = "/addons/promode/";
            $.colorbox({width:"450px", href: url, title: response.app_title, fixed:true});
        }

      if (response.hasOwnProperty('html')) {

        if ($("header.info-banner").length > 0) {

          $("header.info-banner").remove();
          $("div.content-list-page").prepend("<ul class=\"cards-list content-list\"></ul>");
        }

        $("ul.content-list").append(response.html);
        $("input[name=message_text]").val("");
        $("input[name=message_image]").val("");
          $("div.msg-image-preview-container").hide();
          $("img.msg-image-preview").attr("src", "");
          $("i.msg-image-change").html("image");

          if (response.hasOwnProperty('chat_id') && chat_id == 0) {

              chat_id = response.chat_id;
              App.chatInit(chat_id, user_id, access_token);
          }
      }

      if (response.hasOwnProperty('items_all')) {

        items_all = response.items_all;
        items_loaded = $('li.message-item').length;
      }
    },
    error: function(xhr, type){

    }
  });
};

Messages.more = function (chat_id, user_id) {

  var message_id = $("li.message-item").first().attr("data-id");

  $('header.loading-banner').hide();

  $.ajax({
    type: 'POST',
    url: '/ajax/msgMore.php',
    data: "chat_id=" + chat_id + "&user_id=" + user_id + "&message_id=" + message_id + "&messages_loaded=" + items_loaded,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

        $('header.loading-banner').remove();

      if (response.hasOwnProperty('html')) {

        $("ul.content-list").prepend(response.html);
      }

      if (response.hasOwnProperty('html2')) {

        $("div.content-list-page").prepend(response.html2);
      }

      if (response.hasOwnProperty('items_all')) {

        items_all = response.items_all;
        items_loaded = $('li.message-item').length;
      }
    },
    error: function(xhr, type){

        alert("error");
        $('header.loading-banner').show();
    }
  });
};

Messages.removeChat = function(chat_id, user_id, access_token) {

  $.ajax({
    type: 'POST',
    url: '/ajax/chatRemove.php',
    data: 'access_token=' + access_token + "&chat_id=" + chat_id + "&user_id=" + user_id,
    dataType: 'json',
    timeout: 30000,
    success: function(response){

      $('li.chat-item[data-id=' + chat_id + ']').remove();
    },
    error: function(xhr, type){


    }
  });
};

Messages.changeChatImg = function(title) {

  var img_url = $("img.msg_img_preview").attr("src");
  var def_url = "/img/camera.png";

  var i = img_url.localeCompare(def_url);

  if (i != 0) {

    $('input[name=message_image]').val("");
    $("img.msg_img_preview").attr("src", def_url);

    return;
  }

  var url = "/ajax/msg/method/uploadImg.php?action=get-box";

    $.colorbox({width:"450px", href: url, title: title, overlayClose: false, fixed:true, onComplete: function(){

        $('.file_select_btn').upload({
            name: 'userfile',
            method: 'post',
            enctype: 'multipart/form-data',
            action: '/ajax/msg/method/uploadImg.php',
            onComplete: function(text) {

                var response = JSON.parse(text);

                if (response.hasOwnProperty('error')) {

                    if (response.error === false) {

                        $.colorbox.close();

                        if (response.hasOwnProperty('imgUrl')) {

                            $("input[name=message_image]").val(response.imgUrl);
                            $("img.msg_img_preview").attr("src", response.imgUrl);
                        }
                    }
                }

                $("div.file_loader_block").hide();
                $("div.file_select_block").show();
            },
            onSubmit: function() {

                $("div.file_select_block").hide();
                $("div.file_loader_block").show();
            }
        });
    }});
};
