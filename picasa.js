/**
 * Picasa Plugin
 *
 * Copyright (c) 2009 YiXia SUN (e-xia.com)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Display Picasa album content, get image of album link by click.
 *
 * @example $('#picasa').getPicasa({username:'tester'});
 * @desc Display Picasa album content, get image of album link by click.
 *
 * @param Object options An object literal containing key/value pairs to provide optional picasa attributes.
 * @option String username Picasa username.
 * @option Boolean albuminsert Add insert albumn link.
 * @option Array picinsertsize Pick the size of inserted picture.
 * @option Boolean picinsert Add insert picture links.
 * @type undefined
 *
 * @name $.fn.getPicasa
 * @cat Plugins/Picasa
 * @author YiXia SUN/yixia.sun@gmail.com
 */

(function($){
var options = {};
var obj;
$.fn.getPicasa = function(opt){
    obj = $(this);
    options = $.extend({}, $.fn.getPicasa.defaults, opt);
    if(!options.username){
      obj.html($.fn.getPicasa.text.emptyuser);
      return;
    }
    getJSON();
};

$.fn.getPicasa.defaults = {
  albuminsert:false,
  picinsertsize:['400'],
  picinsertmulti:false
}

$.fn.getPicasa.text = {
  emptyuser:"empty user",
  loading:"loading...",
  enter:"Enter",
  insert:"Insert",
  backtoalbum:"Back to Album List",
  insertall:"Insert All Selected Images: ",
  selectc:"Select: ",
  insertc:"Insert: "
}

function getJSON(){
  var albumid = options.albumid;
  var username = options.username;
  var uri = getJSONURI(username,albumid);
  obj.html($.fn.getPicasa.text.loading);
  $.getJSON(
    uri,
    function(json){
      if(albumid) formatPicasa(json);
      else formatPicasaAlbum(json);
    });
}

function getJSONURI(userid, albumid) {
  uri = "http://picasaweb.google.com/data/feed/api/user/" + userid;
  if (albumid) uri += "/albumid/" + albumid;
  uri += "?alt=json-in-script&callback=?";
  return uri;
}

function formatPicasaAlbum(json){
  obj.html("");
  $(json.feed.entry).each(function (i, item){
    var title = item.title.$t;
    var summary = item.summary.$t;
    var imageurl = item.media$group.media$thumbnail[0].url;
    var link = item.link[1].href;
    var albumid = item.gphoto$id.$t;
    obj.append("<div id='a" + albumid + "'><a href='" + link + "'><img src='" + imageurl.replace("/s160-c/","/s144-c/") + "'/></a><br />" + title + (options.albuminsert?"<br /><span class='enter'>" + $.fn.getPicasa.text.enter + "</span> <span class='insert'>" + $.fn.getPicasa.text.insert + "</span>":"") + "</div>"); 
  });
  $('a,.enter',obj).click(function(event) {
    event.preventDefault();
    var id = $(this).parent('div').attr("id").substr(1);
    options.albumid = id;
    getJSON();
  });
  $('.insert').click(function(){
    var uri = $(this).siblings("a").eq(0).attr("href");
    $.fn.getPicasa.insertAlbum(uri);
  });
}

function formatPicasa(json){
  obj.html("");
  $("<span>" + $.fn.getPicasa.text.backtoalbum + "</span>").appendTo(obj).click(function(){
    obj.getPicasa({username:options.username});
  });
  size = options.picinsertsize;
  sizespans = "";
  for (i=0;i<size.length;i++){
    sizespans += " <span class='s" + size[i] + "'>" + size[i] + "</span>";
  }
  if(options.picinsertmulti){
    obj.append("<br />" + $.fn.getPicasa.text.insertall)
    $(sizespans).appendTo(obj);
    $('span').click(function(){
      $.fn.getPicasa.insertImages($("img.selected"),$(this).attr("class"));
    });
    obj.append("<br />");
  }
  $(json.feed.entry).each(function (i, item){
    var title = item.title.$t;
    var summary = escapeHTML(item.summary.$t);
    var imageurl = item.media$group.media$thumbnail[1].url;
    obj.append("<div><span class='img'><img src='" + imageurl + "' alt='" + summary + "' /></span>" + (options.picinsertmulti?$.fn.getPicasa.text.selectc + "<input type='checkbox' /><br />":"") + ((options.picinsertmulti || size.length > 1)?$.fn.getPicasa.text.insertc + sizespans + "</div>":""));
  });
  $('div',obj).each(function(){
    var img = $("img", $(this))
    var check = $("input", $(this))
    $("span:not(.img)", $(this)).click(function(){
      $.fn.getPicasa.insertImages(img,$(this).attr("class"));
    });
    if(options.picinsertmulti){
      img.toggle(function(){
          $(this).addClass("selected");
          check.attr("checked","checked");
        },function(){
          $(this).removeClass("selected");
          check.removeAttr("checked");
        }
      );
    }else if(size.length == 1){
      img.click(function(){
        $.fn.getPicasa.insertImages(img,"s" + size[0]);
      });
    }
    check.click(function(){img.click();})
  });
}

$.fn.getPicasa.insertAlbum = function(str){
  alert(str);
}

$.fn.getPicasa.insertImages = function(images,size){
  arr = jQuery.map(images, function(n){
      return ($(n).attr("src").replace("/s144/","/"+size+"/"));
    });
  alert(arr.join("; "));
}

function escapeHTML(str){
  var div = document.createElement('div');
  var text = document.createTextNode(str);
  div.appendChild(text);
  return div.innerHTML;
};
})(jQuery);