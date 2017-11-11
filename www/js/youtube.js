// Оригинал - https://1chan.ca/news/res/14639/#14714
function addJQuery(callback) {
    var script = document.createElement("script");
        script.setAttribute("src", "/js/jquery-2.1.1.min.js");
        script.addEventListener('load', function() {
            var script = document.createElement("script");
            script.textContent = "window.jQ=jQuery.noConflict(true);(" + callback.toString() + ")();";
            document.head.appendChild(script);
        }, false);
    document.head.appendChild(script);
}
function myUserScript() {
jQ(function() {
// start of userscript =================================================
// Для ссылок в комментариях
jQ('p > a[href*=youtu]').each(function(){
    var thisPostID   = jQ(this).parent('p')
                                   .parent('div')
                                   .parent('div')
                                   .attr('id')
                                   .match(/(\d+)/);
    var videoID      = jQ(this).attr('href')
                                   .match(/(v=)?(%3D)?(\/)?(%2F)?([_\-\d\w+]{11})/);
    var videoSRC     = 'https://www.youtube-nocookie.com/embed/' + videoID[5];
    var imageSRCbig  = 'https://i.ytimg.com/vi/' + videoID[5] + '/0.jpg';
    var imageSRCmini = 'https://i.ytimg.com/vi/' + videoID[5] + '/default.jpg';
    var videoBlockID = 'video' + thisPostID[0] + videoID[5];
    var imageBlockID = 'image' + thisPostID[0] + videoID[5];

    var videoHTML = jQ('<embed/>')
        .attr({ 'type'   : '',
                'width'  : '560',
                'height' : '315',
                'src'    : videoSRC,
                'wmode'  : 'transparent' });

    var closeButton = jQ('<button/>')
        .click(function () {
            jQ('#'+videoBlockID).slideUp(1000);
            jQ('#'+imageBlockID).slideDown(1000);
            })
        .text('X');
    
    var imageHTMLbig = jQ('<img/>')
        .attr({ 'src'    : imageSRCbig })
        .css({  'width'  : '560',
                'height' : '315' });

    var imageHTMLmini = jQ('<img/>')
        .attr({ 'src'    : imageSRCmini });

    jQ('<div/>')
        .attr({ 'id'     : videoBlockID })
        .css({  'display': 'none' })
        .append(videoHTML)
        .append(closeButton)
        .insertAfter(this);

    jQ('<div/>')
        .attr({ 'id'     : imageBlockID })
        .append(imageHTMLmini)
        .click(function () {
            jQ(this).slideUp(1000);
            jQ('#'+videoBlockID).slideDown(1000);
            })
        .insertAfter(this);

});


// End of userscript ===================================================
});
}
addJQuery(myUserScript);
