'use strict';

$.fn.upload = function (param) {
    $("<input>", {type: "file"}).change(function () {
        var formData = new FormData();
        formData.append("upload", $(this)[0].files[0]);
        formData.append("type", param.type);
        $.ajax({
            url: param.url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: param.success,
            error: param.error
        });
    }).click();
};

function getFileIcon(fileName) {
    var ext = fileName.substr(fileName.lastIndexOf(".") + 1).toLowerCase(),
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
    return maps[ext] ? maps[ext] : maps["txt"];
}

function parseFile(file, editable) {
    var iconUrl = "/img/fileTypeImages/" + getFileIcon(file["fileName"]),
        hash = file["hash"],
        url = file["url"],
        fileName = file["fileName"];
    var html = $("<p>").css("line-height", "16px").append(
        $("<img>").css("vertical-align", "middle").css("margin-right", "2px").attr("src", iconUrl)
    ).append(
        $("<a>").css("font-size", "12px").css("color", "#0066CC").attr("href", url).attr("title", fileName).text(fileName)
    );
    editable = editable || false;
    if (editable) {
        html.append(
            $("<input>").css("display", "none").attr("name", "attachment[]").val(hash)
        ).append(
            $("<span>").addClass("glyphicon glyphicon-remove")
                .css("color", "red").css("display", "inline-block").css("cursor", "pointer")
                .click(function () {
                    $(this).parent().remove();
                })
        );
    }
    return html;
}