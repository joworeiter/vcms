/* animate inserted elements */

function animateLasteInsertId(){
	$(".lastInsertId").slideDown(1000);
}

/* adjust thumbnails */

function adjustThumbnailImgsOnReady(){
	$(".thumbnail .thumbnailOverflow img").load(function() {
		adjustThumbnailImg($(this));
	}).each(function() {
	  if(this.complete) $(this).load();
	});
}

function adjustThumbnailImgsOnResize(){
	$(".thumbnail .thumbnailOverflow img").each(function() {
		adjustThumbnailImg($(this));
	});
}

function adjustThumbnailImg(thumbnailImg){
	adjustThumbnailOverflowHeight(thumbnailImg);
	adjustThumbnailImgMarginTop(thumbnailImg);
}

function adjustThumbnailOverflowHeight(thumbnailImg){
	var thumbnailOverflow = thumbnailImg.closest('.thumbnailOverflow');
	var width = thumbnailOverflow.width();
	var height = width / 3 * 2;
	thumbnailOverflow.height(height);
}

function adjustThumbnailImgMarginTop(thumbnailImg){
	var thumbnailOverflow = thumbnailImg.closest('.thumbnailOverflow');
	var thumbnailOverflowHeight = thumbnailOverflow.height();
	var thumbnailImgHeight = thumbnailImg.height();
	
	if(thumbnailImgHeight > 0){
		var marginTop = (thumbnailOverflowHeight - thumbnailImgHeight) / 2;
		thumbnailImg.css('margin-top', marginTop + 'px');
	}
}

/* adjust facebook plugins */

function adjustFacebookPagePluginsSrc(){
	$("iframe.facebookPagePlugin").each(function() {
		var iframe = $(this);
		var width = iframe.width();
		var src = iframe.attr('src');
		var newSrc = src.replace(/width=[0-9]+/, 'width=' + width);
		
		iframe.attr('src', newSrc);
	});
}

function adjustFacebookEventPluginsHeight(){
	$("iframe.facebookEventPlugin").each(function() {
		adjustFacebookEventPluginHeight(this);
	});
}

function adjustFacebookEventPluginHeight(iframe){
	var iframewindow = iframe.contentWindow? iframe.contentWindow : iframe.contentDocument.defaultView;
	var contentHeight = iframewindow.document.body.scrollHeight + "px";
	iframe.height = contentHeight;
}

// --------------------

$(document).ready(function() {
	animateLasteInsertId();
	adjustThumbnailImgsOnReady();

	adjustFacebookPagePluginsSrc();
	adjustFacebookEventPluginsHeight();

	$("iframe.facebookEventPlugin").load(function(){
		adjustFacebookEventPluginHeight(this);
	});
});

var resizeDebounce;

$(window).resize(function() {
	clearTimeout(resizeDebounce);
	resizeDebounce = setTimeout(function(){
		adjustThumbnailImgsOnResize();
		adjustFacebookPagePluginsSrc();
		adjustFacebookEventPluginsHeight();
	}, 500);
});