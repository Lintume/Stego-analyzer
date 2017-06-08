var GalleryEncode = new Vue({
    el: '#LSBEncode',
    data: {
        loadingEncode: false,
        pictures: {
            original: "",
            containers: []
        },
        picture: {
            base64Picture: "",
            bytes: null
        },
        methods: [],
        analyseUrl: "",
        errors: [],
        text: "",
        password: "",
        offset: 0,
        maxlength: 0,
        lengthText: 0,
        seconds: 0
    },
    mounted: function () {
        this.analyseUrl = analyseUrlEncode;
    },
    watch: {
        text: function (text) {
            this.lengthText = text.length
        }
    },
    methods: {
        copyPicture: function () {
            var copiedPicture = jQuery.extend(true, {}, this.picture)
            this.pictures.containers.push(copiedPicture);
        },
        onImageChangeOrig: function (event) {
            var files = event.target.files || event.dataTransfer.files;
            if (!files.length)
                return;

            var fileTypes = ['jpg', 'jpeg', 'png'];
            var extension = files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
                isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types

            if (isSuccess) {
                this.readFileOrig(files[0]);
            }
            else {
                alert('It is not a picture. Please, use a picture')
            }
            event.preventDefault()
        },
        readFileOrig: function (file) {
            var self = this;
            var reader = new FileReader();
            reader.onloadend = function () {
                self.pictures.original = reader.result;
            };
            if (file) {
                reader.readAsDataURL(file);
            }
            setTimeout(function(){
            var imgData = self.pictures.original;

            var img = new Image();
                img.src = imgData;
                img.onload = function () {
                    self.maxlength = (((img.width * img.height) / 8) -8).toFixed();
                    $("#textarea").attr('maxlength', self.maxlength);
                }
            }, 100);
        },
        sendOnSever: function (event) {
            var timerStart = Date.now();
            event.preventDefault()
            var self = this;
            if(this.loadingEncode == false) {
                this.loadingEncode = true;
                this.$http.post(this.analyseUrl,
                    {
                        'pictures': this.pictures,
                        'text': this.text,
                        'password': this.password,
                        'offset': this.offset
                    })
                    .then(function(response) {
                        // debugger;
                        if(response.body.data) {
                            this.copyPicture();
                            this.pictures.containers[0].base64Picture = response.body.data;
                        }
                        this.loadingEncode = false;
                        this.seconds = (Date.now()-timerStart)/1000;
                    }, function (response) {
                        this.$set(this, 'errors', response.body);
                        alert("Errors in form");
                        this.loadingEncode = false;
                    });
            }
        }
    }
})