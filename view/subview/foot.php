<?php namespace NoCon\Framework; // namespace needed to access classes ?>

<!-- footer start -->
<hr>
<!-- footer end -->

<script src="<?php echo Router::$ARGS['JS_URL']; ?>angular.min.js"></script>
<script>
var cameraApp = angular.module('cameraApp', []);

cameraApp.controller('CameraController', function ($scope, $http, $filter) {
    $scope.busy = false;
    $scope.infotext = '';
    $scope.currentTab = 'details';
    $scope.devices = [];
    $scope.device = null;
    $scope.systemDateAndTime = null;
    $scope.ntpInformation = null;
//    $scope.ntpFromDHCP = null;
    $scope.networkInterfaces = null;
    

    $scope.tabs = [
        {'title': 'Details', 'name': 'details'},
        {'title': 'SystemDateAndTime', 'name': 'systemdateandtime'},
        {'title': 'NTPInformation', 'name': 'ntpinformation'},
        {'title': 'NetworkInterfaces', 'name': 'networkinterfaces'},
        {'title': 'Reset', 'name': 'reset'}
    ];
    
    
    $scope.showDetails = [
        'Model',
        'Manufacturer',
        'HardwareId',
        'SerialNumber',
        'IPAddrs',
        'XAddrs',
        'FirmwareVersion'
    ];
    
    
    
    /**
     * Send a device request.
     * 
     * @param {string} params The URL parameters to append to the request URL.
     * @param {function} callback The callback function to process the returned data.
     */
    /*
    $scope.deviceRequest = function(method, params, callback) {
        if ( $scope.busy ) {
            return;
        }
        
        $scope.busy = true;
        
        uri = 'devicerequest/' + method + (params && params.length ? '/' + params : '');
        
        $http.get(uri).then(
                function(response) {
                    callback(response.data);
                    $scope.busy = false;
                },
                function(response) {
                    $scope.busy = false;
                }
        );
    };
    */
    
    
    $scope.deviceRequestP = function(method, xaddrs, data, callback) {
        if ( $scope.busy ) {
            return;
        }
        
        $scope.busy = true;
        
        uri = 'devicerequest/' + method;
        postData = {
            'Method': method,
            'XAddrs': xaddrs,
            'Data': data
        };
        $http.post('devicerequest/', postData).then(
            function(response) {
                if ( angular.isFunction(callback) ) {
                    callback(response.data);
                }
                $scope.busy = false;
            },
            function(response) {
                $scope.busy = false;
            }
        );
    };
    
    
    
    $scope.discoverCameras = function() {
        $scope.deviceRequestP('discover',null, null, function(data) {
            if ( data.discoverList ) {
                $scope.devices = data.discoverList;
            }
        });
    };
    
    
    $scope.getAll = function() {
        $scope.deviceRequestP('getAll', $scope.device.XAddrs, null, function(data) {
            if ( data.SystemDateAndTime ) {
                $scope.systemDateAndTimeToScope(data.SystemDateAndTime);
            }
            
            if ( data.NTPInformation ) {
                $scope.ntpInformationToScope(data.NTPInformation);
            }
            
            if ( data.NetworkInterfaces ) {
                $scope.networkInterfaces = data.NetworkInterfaces;
            }
        });
    };
    
    
    $scope.getSystemDateAndTime = function() {
        $scope.deviceRequestP('getSystemDateAndTime', $scope.device.XAddrs, null, function(data) {
            if ( data.SystemDateAndTime ) {
                $scope.systemDateAndTimeToScope(data.SystemDateAndTime);
            }
        });
    };
    
    
    $scope.adjustDateTime = function() {
        $scope.setCurrentDateTime($scope.systemDateAndTime.UTCDateTimeString);
    };
    
    
    $scope.setCurrentDateTime = function(dateTimeString) {
        var d = new Date(typeof dateTimeString === 'undefined' ? Date.now() : dateTimeString);
        $scope.deviceRequestP('setUTCDateTime', $scope.device.XAddrs, {'UTCDateTimeString': d.toISOString()}, function(data) {
            if ( data.SystemDateAndTime ) {
                $scope.systemDateAndTimeToScope(data.SystemDateAndTime);
            }
        });
    };
    
    
    $scope.setTimeZone = function() {
        $scope.deviceRequestP('setTimeZone', $scope.device.XAddrs, $scope.systemDateAndTime['TimeZone']['TZ'], function(data) {
            if ( data.SystemDateAndTime ) {
                $scope.systemDateAndTimeToScope(data.SystemDateAndTime);
            }
        });
    };
    
    
    $scope.setDaylightSavings = function() {
        $scope.deviceRequestP('setDaylightSavings', $scope.device.XAddrs, $scope.systemDateAndTime.DaylightSavings, function(data) {
            if ( data.SystemDateAndTime ) {
                $scope.systemDateAndTimeToScope(data.SystemDateAndTime);
            }
        });
    };
    
    
    $scope.setDateTimeType = function() {
        $scope.deviceRequestP('setDateTimeType', $scope.device.XAddrs, {'DateTimeType': $scope.systemDateAndTime.DateTimeType}, function(data) {
            if ( data.SystemDateAndTime ) {
                $scope.systemDateAndTimeToScope(data.SystemDateAndTime);
            }
        });
    };
    
    
    $scope.getNTPInformation = function() {
        $scope.deviceRequestP('getNTPInformation', $scope.device.XAddrs, null, function(data) {
            if ( data.NTPInformation ) {
                $scope.ntpInformationToScope(data.NTPInformation);
            }
        });
    };
    
    
    $scope.setNTPFromDHCP = function() {
        $scope.deviceRequestP('setNTPFromDHCP', $scope.device.XAddrs, {'DateTimeType': $scope.ntpInformation.FromDHCP}, function(data) {
            if ( data.NTPInformation ) {
                $scope.ntpInformationToScope(data.NTPInformation);
            }
        });
    };
    
    
    $scope.setNTPInformation = function() {
        $scope.deviceRequestP('setNTPInformation', $scope.device.XAddrs, $scope.ntpInformation, function(data) {
            if ( data.NTPInformation ) {
                $scope.ntpInformationToScope(data.NTPInformation);
            }
        });
    };
    
    
    $scope.newNTPManual = function() {
        $scope.ntpInformation.NTPManual.push({'Type': 'IPv4', 'IPv4Address': ''});
    }
    
    
    $scope.setNetworkInterfaces = function(index) {
        var interfaceSettings = {
            'InterfaceToken': $scope.networkInterfaces[index].token,
            'NetworkInterface': {
                'IPv4': {
                    'Enabled': $scope.networkInterfaces[index].IPv4.Enabled,
                    'DHCP': $scope.networkInterfaces[index].IPv4.Config.DHCP
                }
            }
        };
        
        if ( $scope.networkInterfaces[index].IPv4.Config.Manual.Address.length ) {
            interfaceSettings.NetworkInterface.IPv4.Manual = $scope.networkInterfaces[index].IPv4.Config.Manual;
        }
        
        $scope.deviceRequestP('setNetworkInterfaces', $scope.device.XAddrs, interfaceSettings, function(data) {
            if ( data.NetworkInterfaces ) {
                $scope.networkInterfaces = data.NetworkInterfaces;
            }
        });
    };
    
    
    $scope.systemReboot = function() {
        $scope.deviceRequestP('systemReboot', $scope.device.XAddrs, null, function(data) {
            
        });
    };
    
    
    $scope.reset = function(type) {
        $scope.deviceRequestP('setSystemFactoryDefault', $scope.device.XAddrs, type, function(data) {
            
        });
    };
    
    
    
    
    $scope.ntpInformationToScope = function(ntpInformation) {
        if ( ntpInformation.NTPManual.length ) {
            ntpInformation.NTPManual.forEach(function(part, index, theArray) {
                if ( typeof part.Type !== 'undefined' ) {
                    return;
                }
                else if ( typeof part.IPv4Address !== 'undefined' ) {
                    theArray[index].Type = 'IPv4';
                }
                else if ( typeof part.IPv6Address !== 'undefined' ) {
                    theArray[index].Type = 'IPv6';
                }
            });
        }
        $scope.ntpInformation = ntpInformation;
    };
    
    
    $scope.systemDateAndTimeToScope = function(systemDateAndTime) {
        systemDateAndTime.TimeZone.TZ = $scope.utcTZ(systemDateAndTime.TimeZone.TZ);
        systemDateAndTime.LocalDateTimeString = $scope.isoDateTime(systemDateAndTime.LocalDateTime); 
        systemDateAndTime.UTCDateTimeString = $scope.isoDateTime(systemDateAndTime.UTCDateTime) + 'Z'; 
        $scope.systemDateAndTime = systemDateAndTime;
    };
    
    
    /**
     * Get an array of POSIX UTC time zone options.
     * 
     * @returns {Array} POSIX UTC time zone strings.
     */
    $scope.utcOptions = function() {
        var options = [];
        for ( var tz = -12; tz <= 12; tz++ ) {
            options.push('UTC' + (tz >= 0 ? '+' + tz : tz));
        }
        return options;
    };
    
    
    /**
     * Normalize the POSIX time zone string to a UTC based value.
     * 
     * @param {String} posixTZ The POSIX time zone value.
     * @returns {String}
     */
    $scope.utcTZ = function(posixTZ) {
        var tzNumber = parseInt(posixTZ.replace(/[a-z]*([-+]{0,1})([0-9]+)/i, '$1$2'));
        return 'UTC' + (Number.isNaN(tzNumber) || tzNumber >= 0 ? '+' : '') + (Number.isNaN(tzNumber) ? '0' : tzNumber);
    };
    
    
    $scope.isoDateTime = function(dateTime) {
        return dateTime.Date.Year + '-' +
            $scope.zeroPad(dateTime.Date.Month, 2) + '-' +
            $scope.zeroPad(dateTime.Date.Day, 2) + 'T' +
            $scope.zeroPad(dateTime.Time.Hour, 2) + ':' +
            $scope.zeroPad(dateTime.Time.Minute, 2) + ':' +
            $scope.zeroPad(dateTime.Time.Second, 2);
    };
    
    
    $scope.zeroPad = function(str, len) {
        while ( str.toString().length < len ) {
            str = '0' + str;
        }
        return str;
    };
    
    
    $scope.test = function() {
        $scope.deviceRequestP('test', $scope.device.XAddrs, null, function(data) {
            
        });
    };
    
    
    $scope.selectDevice = function(item) {
        $scope.device = item;
        $scope.getAll();
    };
    
    
    $scope.selectTab = function(tabName) {
        $scope.currentTab = tabName;
    };
    
});
</script>

<script>
/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
var Base64 = {
    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }

        output = Base64._utf8_decode(output);
        return output;
    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }

        return string;
    }
}
</script>
</body>
</html>