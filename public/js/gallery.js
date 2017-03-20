var Gallery = new Vue({
    el: '#gallery',
    data: {
        loading: false,
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
        errors: []
    },
    mounted: function () {
        this.copyPicture();
        this.analyseUrl = analyseUrl;
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
        },
        sendOnSever: function (event) {
            event.preventDefault()
            var self = this;
            if(this.loading == false) {
                this.loading = true;
                this.$http.post(this.analyseUrl,
                    {
                        'pictures': this.pictures
                    })
                    .then(function(response) {
                        if(response.body.crypto) {
                            this.$set(this, 'errors', null);
                            this.$set(this, 'methods', response.body.crypto);
                            var crypto = [];
                            crypto.IF = [];
                            for (var key in self.methods.IF.coefficients) {
                                crypto.IF.push([Number(key), self.methods.IF.coefficients[key]])
                            }

                            crypto.SNR = [];
                            for (var key in self.methods.SNR.coefficients) {
                                crypto.SNR.push([Number(key), self.methods.SNR.coefficients[key]])
                            }

                            crypto.NC = [];
                            for (var key in self.methods.NC.coefficients) {
                                crypto.NC.push([Number(key), self.methods.NC.coefficients[key]])
                            }

                            crypto.NAD = [];
                            for (var key in self.methods.NAD.coefficients) {
                                crypto.NAD.push([Number(key), self.methods.NAD.coefficients[key]])
                            }
                            google.charts.load('current', {'packages': ['corechart']});
                            google.charts.setOnLoadCallback(drawChart);
                            function drawChart() {
                                var data = google.visualization.arrayToDataTable(crypto.IF, true);
                                var options = {
                                    title: 'IF',
                                    hAxis: {title: 'Bits', titleTextStyle: {color: '#333'}},
                                    vAxis: {minValue: self.methods.IF.min, maxValue: self.methods.IF.max}
                                };
                                var chart = new google.visualization.AreaChart(document.getElementById('chart_divIf'));
                                chart.draw(data, options);
                                $("a[href='#IF']").on('shown.bs.tab', function (e) {
                                    chart.draw(data, options);
                                });

                                var data_snr = google.visualization.arrayToDataTable(crypto.SNR, true);
                                var options_snr = {
                                    title: 'SNR',
                                    hAxis: {title: 'Bits', titleTextStyle: {color: '#333'}},
                                    vAxis: {minValue: self.methods.SNR.min, maxValue: self.methods.SNR.max}
                                };
                                var chart_snr = new google.visualization.AreaChart(document.getElementById('chart_div_snr'));
                                $("a[href='#SNR']").on('shown.bs.tab', function (e) {
                                    chart_snr.draw(data_snr, options_snr);
                                });

                                var data_nc = google.visualization.arrayToDataTable(crypto.NC, true);
                                var options_nc = {
                                    title: 'NC',
                                    hAxis: {title: 'Bits', titleTextStyle: {color: '#333'}},
                                    vAxis: {minValue: self.methods.NC.min, maxValue: self.methods.NC.max}
                                };
                                var chart_nc = new google.visualization.AreaChart(document.getElementById('chart_div_nc'));
                                $("a[href='#NC']").on('shown.bs.tab', function (e) {
                                    chart_nc.draw(data_nc, options_nc);
                                });

                                var data_nad = google.visualization.arrayToDataTable(crypto.NAD, true);
                                var options_nad = {
                                    title: 'NAD',
                                    hAxis: {title: 'Bits', titleTextStyle: {color: '#333'}},
                                    vAxis: {minValue: self.methods.NAD.min, maxValue: self.methods.NAD.max}
                                };
                                var chart_nad = new google.visualization.AreaChart(document.getElementById('chart_div_nad'));
                                $("a[href='#NAD']").on('shown.bs.tab', function (e) {
                                    chart_nad.draw(data_nad, options_nad);
                                });
                            }
                        }
                        this.loading = false;
                    }, function (response) {
                        this.$set(this, 'errors', response.body);
                        alert("Errors in form");
                        this.loading = false;
                    });
            }
        }
    }
})