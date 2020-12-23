<form action="{{route('upload.images')}}" method="post"
      class="dropzone" id="">
    @csrf
    {{--   path like this :  "assets/uploads/gyms/" --}}
    <input type="hidden" name="path" value="{{$pathDir}}">
    <div class="fallback">
        <input name="file" type="file" multiple/>
    </div>
</form>
<script src="{{asset('assets/plugins/dropzone/dropzone.js')}}"></script>

<script>
    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone(".dropzone", {
        autoProcessQueue: true,
        addRemoveLinks: true,
        acceptedFiles: 'image/*',
        parallelUploads: 10, // Number of files process at a time (default 2)
        maxFiles: 6,
        init: function () {
            myDropzone = this;

            var imageUrls = $('{{$imagesInputId}}').val();
            if (imageUrls) {
                $(JSON.parse(imageUrls)).each(function (i, ImgUrl) {
                    var filename = ImgUrl.split('/')[3];
                    var mockFile = {name: filename, size: 33.5};
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.emit("thumbnail", mockFile, '/' + ImgUrl);
                    myDropzone.emit("complete", mockFile);
                })
            }
            this.on("sending", function(file, xhr, formData) {
                // Will send the filesize along with the file as POST data.
                formData.append("filesize", file.size);
                formData.append("fileName", "myName");
            });

            this.on("success", function (file, res) {
                console.log( res.image_url);
                var allImagesUrl = [];
                var imageUrls = $('{{$imagesInputId}}').val();
                if (imageUrls) {
                    $(JSON.parse(imageUrls)).each(function (i, obj) {
                        allImagesUrl.push(obj)
                    })
                }
                allImagesUrl.push(res.image_url);
                $('{{$imagesInputId}}').val(JSON.stringify(allImagesUrl));


                {{--var imageUrls = $('{{$imagesInputId}}').val();--}}
                {{--if (imageUrls) {--}}
                {{--    $(JSON.parse(imageUrls)).each(function (i, ImgUrl) {--}}
                {{--        var filename = ImgUrl.split('/')[3];--}}
                {{--        var mockFile = {name: filename, size: 33.5};--}}
                {{--        myDropzone.emit("addedfile", mockFile);--}}
                {{--        myDropzone.emit("thumbnail", mockFile, '/' + ImgUrl);--}}
                {{--        myDropzone.emit("complete", mockFile);--}}
                {{--    })--}}
                {{--}--}}
            });

            this.on("error", function (file, error, xhr) {
                notification('danger', error);
                // console.log('error',error);
            });

            this.on("removedfile", function (file) {

                var allImagesUrl = [];
                var imageUrls = $('{{$imagesInputId}}').val();
                if (imageUrls) {
                    $(JSON.parse(imageUrls)).each(function (i, url) {
                        if (url != '{{$pathDir}}' + file.name) {
                            allImagesUrl.push(url)
                        }
                    })
                }
                $('{{$imagesInputId}}').val(JSON.stringify(allImagesUrl));

                var path = '{{$pathDir}}/'.replace('/', '*').replace('/', '*').replace('/', '*').replace('/', '*');
                $.ajax({
                    url: '/remove/image/' + file.name + '/' + path,
                    type: "GET",
                    success: function (res) {
                    }
                });

            })
        }
    });

</script>
