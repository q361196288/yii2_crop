jQuery(function () {
    var $ = jQuery;
    $.fn.crop_upload = function (config) {
        $("body").append(renderModal());

        var avatar = document.getElementById(config['avatar_id']);
        var image = document.getElementById(config['image_id']);
        var input = $("#" + config['input_id'])//document.getElementById(config['input_id']);
        var $progress = $('.progress');
        var $progressBar = $('.progress-bar');
        var $alert = $('.alert');
        var $modal = $('#' + config['modal_id']);
        var cropper;
        var label = $('[data-toggle="' + config['tooltip'] + '"]')
        label.tooltip();
        input.on('change', function (e) {
            var files = e.target.files;
            var done = function (url) {
                input.value = '';
                image.src = url;
                $alert.hide();
                $modal.modal('show');
            };
            var reader;
            var file;
            var url;

            if (files && files.length > 0) {
                file = files[0];

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
        $("#" + config['avatar_id']).next().on('click', function () {
            label.prev().val("");
            avatar.src = "";
            input.outerHTML = input.outerHTML;
        })
        $modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: config['width'] / config['height'],
                viewMode: 3,
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });

        $modal.children().children().children(".modal-footer").children(".btn-primary").on('click', function () {
            var initialAvatarURL;
            var canvas;

            $modal.modal('hide');

            if (cropper) {
                canvas = cropper.getCroppedCanvas({
                    width: 305,
                    height: 230,
                });
                initialAvatarURL = avatar.src;
                avatar.src = canvas.toDataURL();
                $progress.show();
                $alert.removeClass('alert-success alert-warning');
                canvas.toBlob(function (blob) {
                    var formData = new FormData();
                    formData.append('file', blob, 'avatar.jpg');
                    for (var key in datas) {
                        formData.append(key, datas[key]);
                    }
                    $.ajax(config['upload_url'], {
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",

                        xhr: function () {
                            var xhr = new XMLHttpRequest();

                            xhr.upload.onprogress = function (e) {
                                var percent = '0';
                                var percentage = '0%';

                                if (e.lengthComputable) {
                                    percent = Math.round((e.loaded / e.total) * 100);
                                    percentage = percent + '%';
                                    $progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                                }
                            };

                            return xhr;
                        },

                        success: function (data) {
                            if (data.code !== 0) {
                                $alert.show().addClass('alert-warning').text(data.msg);
                            } else {
                                label.prev().val(data.attachment);
                                avatar.src = data.url;
                            }
                        },

                        error: function () {
                            avatar.src = initialAvatarURL;

                        },

                        complete: function () {
                            $progress.hide();
                        },
                    });
                });
            }
        });

        function renderModal() {
            var modal_id = "modal";
            if ($("#" + modal_id).length == 0) {
                return '<div class="progress" style="display: none">' +
                    '        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="alert" role="alert"></div>' +
                    '    <div class="modal fade" id="' + config['modal_id'] + '" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">' +
                    '        <div class="modal-dialog" role="document">' +
                    '            <div class="modal-content">' +
                    '                <div class="modal-header">' +
                    '                    <h5 class="modal-title" id="modalLabel">裁切图片</h5>' +
                    '                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                    '                        <span aria-hidden="true">&times;</span>' +
                    '                    </button>' +
                    '                </div>' +
                    '                <div class="modal-body">' +
                    '                    <div class="img-container">' +
                    '                        <img id="' + config['image_id'] + '" src="https://avatars0.githubusercontent.com/u/3456749">' +
                    '                    </div>' +
                    '                </div>' +
                    '                <div class="modal-footer">' +
                    '                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>' +
                    '                    <button type="button" class="btn btn-primary">上传</button>' +
                    '                </div>' +
                    '            </div>' +
                    '        </div>' +
                    '    </div>';
            } else {
                return false;
            }
        }
    }
})
