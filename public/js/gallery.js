var Gallery = new Vue({
    el: '#gallery',
    data: {
        message: "Dick",
        pictures: {
            original: "",
            containers: []
        },
        picture: []
    },
    mounted: function () {
        this.copyPicture()
    },
    methods: {
        copyPicture: function () {
            var copiedPicture = jQuery.extend(true, [], this.picture)
            this.pictures.containers.push(copiedPicture);
        },
        deleteGallery: function (index) {
            this.galleries.splice(index, 1);
        },
        onImageChange: function (event, ig, ip) {
            var files = event.target.files || event.dataTransfer.files;
            if (!files.length)
                return;

            var fileTypes = ['jpg', 'jpeg', 'png'];
            var extension = files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
                isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types

            if (isSuccess) {
                this.readFile(files[0], ig, ip);
                this.autoSizePicture();
            }
            else {
                bootbox.alert('It is not a picture. Please, use a picture')
            }
            event.preventDefault()
        },
        readFile: function (file, ig, ip) {
            var self = this;
            var reader = new FileReader();
            reader.onloadend = function () {
                self.$set('galleries[' + ig + '][\'pictures\'][' + ip + ']', reader.result);
                console.log(typeof self.galleries[ig]['pictures'][ip]);
            };
            if (file) {
                reader.readAsDataURL(file);
            }
            console.log(this.galleries)
        },
        autoSizePicture: function () {
            setTimeout(function(){
                $('.fitPicture').height($('.fitPicture').width())
            }, 100);
        },
        deletePicture: function (event, keyGallery, keyPicture)
        {
            event.preventDefault();
            this.$set('galleries[' + keyGallery + '][\'pictures\'][' + keyPicture + ']', []);
        }
    }
})