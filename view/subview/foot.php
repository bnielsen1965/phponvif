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
    $scope.deviceUsername = null;
    $scope.devicePassword = null;
    $scope.devices = [];
    $scope.device = null;
    $scope.systemDateAndTime = null;
    $scope.ntpInformation = null;
    $scope.networkInterfaces = null;
    $scope.other = null;
    

    $scope.tabs = [
        {'title': 'Details', 'name': 'details'},
        {'title': 'SystemDateAndTime', 'name': 'systemdateandtime'},
        {'title': 'NTPInformation', 'name': 'ntpinformation'},
        {'title': 'NetworkInterfaces', 'name': 'networkinterfaces'},
        {'title': 'Reset', 'name': 'reset'},
        {'title': 'Other', 'name': 'other'}
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
        if ( $scope.ntpInformation.NTPManual.length ) {
            // clear unused types
            $scope.ntpInformation.NTPManual.forEach(function(element, index, array) {
                if ( array[index].Type === 'DNS' ) {
                    delete array[index].IPv4Address;
                    delete array[index].IPv6Address;
                }
                else if ( array[index].Type === 'IPv4' ) {
                    delete array[index].DNSName;
                    delete array[index].IPv6Address;
                }
                else if ( array[index].Type === 'IPv6' ) {
                    delete array[index].DNSName;
                    delete array[index].IPv4Address;
                }
            });
        }
        
        $scope.deviceRequestP('setNTPInformation', $scope.device.XAddrs, $scope.ntpInformation, function(data) {
            if ( data.NTPInformation ) {
                $scope.ntpInformationToScope(data.NTPInformation);
            }
        });
    };
    
    
    $scope.newNTPManual = function() {
        $scope.ntpInformation.NTPManual.push({'Type': 'IPv4', 'IPv4Address': ''});
    };
    
    
    $scope.newIPv4Manual = function(index) {
        if ( $scope.networkInterfaces[index].IPv4.Config.Manual ) {
            $scope.networkInterfaces[index].IPv4.Config.Manual.push({'Address': '', 'PrefixLength': ''});
        }
        else {
            $scope.networkInterfaces[index].IPv4.Config.Manual = [{'Address': '', 'PrefixLength': ''}];
        }
    };
    
    
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
        
        if ( $scope.networkInterfaces[index].IPv4.Config.Manual.length ) {
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
    
    
    $scope.getWsdlUrl = function() {
        $scope.deviceRequestP('getWsdlUrl', $scope.device.XAddrs, null, function(data) {
            if ( data.WsdlUrl ) {
                $scope.other = data.WsdlUrl;
            }
        });
    };
    
    
    $scope.getServices = function() {
        $scope.deviceRequestP('getServices', $scope.device.XAddrs, null, function(data) {
            if ( data.Service ) {
                $scope.other = data.Service;
            }
        });
    };
    
    
    $scope.getCapabilities = function() {
        $scope.deviceRequestP('getCapabilities', $scope.device.XAddrs, null, function(data) {
            if ( data.Capabilities ) {
                $scope.other = data.Capabilities;
            }
        });
    };
    
    
    $scope.getHostname = function() {
        $scope.deviceRequestP('getHostname', $scope.device.XAddrs, null, function(data) {
            if ( data.HostnameInformation ) {
                $scope.other = data.HostnameInformation;
            }
        });
    };
    
    
    $scope.getNetworkProtocols = function() {
        $scope.deviceRequestP('getNetworkProtocols', $scope.device.XAddrs, null, function(data) {
            if ( data.NetworkProtocols ) {
                $scope.other = data.NetworkProtocols;
            }
        });
    };
    
    
    $scope.getSystemUris = function() {
        $scope.deviceRequestP('getSystemUris', $scope.device.XAddrs, null, function(data) {
            if ( data ) {
                $scope.other = data;
            }
        });
    };
    
    
    $scope.getSystemSupportInformation = function() {
        $scope.deviceRequestP('getSystemSupportInformation', $scope.device.XAddrs, null, function(data) {
            if ( data ) {
                $scope.other = data;
            }
        });
    };
    
    
    $scope.getUsers = function() {
        $scope.deviceRequestP('getUsers', $scope.device.XAddrs, null, function(data) {
            if ( data ) {
                $scope.other = data;
            }
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
    
    
    
    
    $scope.selectDevice = function(item) {
        $scope.device = item;
        $scope.getAll();
    };
    
    
    $scope.selectTab = function(tabName) {
        $scope.currentTab = tabName;
    };
    
});
</script>

</body>
</html>