var Gallery = new Vue({
    el: '#gallery',
    data: {
        message: "Dick",
        pictures: {
            original: "",
            containers: []
        },
        picture: {
            base64Picture: "",
            bytes: null
        }
    },
    mounted: function () {
        this.copyPicture()
    },
    methods: {
        copyPicture: function () {
            var copiedPicture = jQuery.extend(true, {}, this.picture)
            this.pictures.containers.push(copiedPicture);
        },
        onImageChange: function (event, ip) {
            var files = event.target.files || event.dataTransfer.files;
            if (!files.length)
                return;

            var fileTypes = ['jpg', 'jpeg', 'png'];
            var extension = files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
                isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types

            if (isSuccess) {
                this.readFile(files[0], ip);
            }
            else {
                alert('It is not a picture. Please, use a picture')
            }
            event.preventDefault()
        },
        readFile: function (file, ip) {
            var self = this;
            var reader = new FileReader();
            reader.onloadend = function () {
                self.pictures.containers[ip].base64Picture = reader.result;
            };
            if (file) {
                reader.readAsDataURL(file);
            }
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
        },
        deletePicture: function (event, keyPicture)
        {
            this.pictures.containers[keyPicture].base64Picture = "";
            this.pictures.containers[keyPicture].bytes = null;
        },
        deletePictureOrig: function (event)
        {
            event.preventDefault();
            this.pictures.original = "";
        }
    }
})