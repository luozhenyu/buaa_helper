'use strict';

function getFileIcon(url) {
    var ext = url.substr(url.lastIndexOf('.') + 1).toLowerCase(),
        maps = {
            "rar": "icon_rar.gif",
            "zip": "icon_rar.gif",
            "tar": "icon_rar.gif",
            "gz": "icon_rar.gif",
            "bz2": "icon_rar.gif",
            "doc": "icon_doc.gif",
            "docx": "icon_doc.gif",
            "pdf": "icon_pdf.gif",
            "mp3": "icon_mp3.gif",
            "xls": "icon_xls.gif",
            "chm": "icon_chm.gif",
            "ppt": "icon_ppt.gif",
            "pptx": "icon_ppt.gif",
            "avi": "icon_mv.gif",
            "rmvb": "icon_mv.gif",
            "wmv": "icon_mv.gif",
            "flv": "icon_mv.gif",
            "swf": "icon_mv.gif",
            "rm": "icon_mv.gif",
            "exe": "icon_exe.gif",
            "psd": "icon_psd.gif",
            "txt": "icon_txt.gif",
            "jpg": "icon_jpg.gif",
            "png": "icon_jpg.gif",
            "jpeg": "icon_jpg.gif",
            "gif": "icon_jpg.gif",
            "ico": "icon_jpg.gif",
            "bmp": "icon_jpg.gif"
        };
    return maps[ext] ? maps[ext] : maps['txt'];
}

function parseFile(file, del) {
    var html, iconDir = '/img/fileTypeImages/';
    var icon = iconDir + getFileIcon(file['fileName']);

    var btn = del ? '<a class="glyphicon glyphicon-remove" style="color:red;display:inline-block" href="javascript:void(0)" onclick="this.parentNode.remove();"></a>' : '';

    html = '<p style="line-height: 16px;" data-sha1="' + file['sha1'] + '">'
        + '<img style="vertical-align: middle; margin-right: 2px;" src="' + icon + '" />'
        + '<a style="font-size:12px; color:#0066cc;" href="' + file['url'] + '" title="' + file['fileName'] + '">'
        + file['fileName']
        + '</a>'
        + btn
        + '</p>';
    return html;
}