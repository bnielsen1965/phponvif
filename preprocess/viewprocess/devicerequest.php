<?php
namespace NoCon\Framework;

// get posted data in JSON format
$post = json_decode(file_get_contents('php://input'), true);

// get URL parameters
$parameters = Router::getParameters();

// determine the method called
$method = (isset($post['Method']) ? $post['Method'] : (empty($parameters[1]) ? null : $parameters[1]));

$includePath = Config::get('framework', 'includePath');

$json = array();
$errors = array();
$messages = array();

switch ($method) {
    case 'discover':
        $discoverList = \NoCon\ONVIF\Device::discover(2, Config::get('onvif', 'bindIPs'));
        foreach ( $discoverList as &$listItem ) {
            $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl', array('trace' => true, 'location' => $listItem['XAddrs'])));//, 'uri' => 'http://www.onvif.org/ver10/device/wsdl')));
            $info = $device->getDeviceInformation();
            $listItem = array_merge($listItem, (array)$info);
        }
        $json['discoverList'] = $discoverList;
        break;
        
        
    case 'getAll':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $json = array_merge($json, getSystemDateAndTime($wsdl, $location));
        $json = array_merge($json, getNTPInformation($wsdl, $location));
        $json = array_merge($json, getNetworkInterfaces($wsdl, $location));
        break;
        
        
    case 'getSystemDateAndTime':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $json = array_merge($json, getSystemDateAndTime($wsdl, $location));
        break;
        
        
    case 'setUTCDateTime':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $dateTime = strtotime($post['Data']['UTCDateTimeString']);
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        
        // need some existing settings before timezone can be set
        $response = $device->getSystemDateAndTime();
        
        $device->setSystemDateAndTime(array(
            'DateTimeType' => $response->SystemDateAndTime->DateTimeType,
            'DaylightSavings' => $response->SystemDateAndTime->DaylightSavings,
            'UTCDateTime' => array(
                'Date' => array(
                    'Year' => date('Y', $dateTime),
                    'Month' => date('n', $dateTime),
                    'Day' => date('d', $dateTime)
                ),
                'Time' => array(
                    'Hour' => date('h', $dateTime),
                    'Minute' => date('i', $dateTime),
                    'Second' => date('s', $dateTime)
                )
            )
        ));
        
        // returning current settings after change
        usleep(500000);
        $json = array_merge($json, getSystemDateAndTime($wsdl, $location));
        break;
        
        
    case 'setTimeZone':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $timeZone = $post['Data'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        
        // need some existing settings before timezone can be set
        $timestamp = getSystemDateAndTime($wsdl, $location);
        
        $device->setSystemDateAndTime(array(
            'DateTimeType' => $timestamp['SystemDateAndTime']['DateTimeType'],
            'DaylightSavings' => $timestamp['SystemDateAndTime']['DaylightSavings'],
            'TimeZone' => array('TZ' => $timeZone)
        ));
        
        // returning current settings after change
        usleep(500000);
        $json = array_merge($json, getSystemDateAndTime($wsdl, $location));
        break;
        
        
    case 'setDaylightSavings':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $daylightSavings = $post['Data'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        
        // need some existing settings before timezone can be set
        $response = getSystemDateAndTime($wsdl, $location);
        
        $device->setSystemDateAndTime(array(
            'DateTimeType' => $response['SystemDateAndTime']['DateTimeType'],
            'DaylightSavings' => $daylightSavings
        ));
        
        // returning current settings after change
        usleep(500000);
        $json = array_merge($json, getSystemDateAndTime($wsdl, $location));
        break;
    
    
    case 'setDateTimeType':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        
        // need some existing settings before timezone can be set
        $response = getSystemDateAndTime($wsdl, $location);
        
        $device->setSystemDateAndTime(array(
            'DateTimeType' => $post['Data']['DateTimeType'],
            'DaylightSavings' => $response['SystemDateAndTime']['DaylightSavings'],
        ));
        
        // returning current settings after change
        usleep(500000);
        $json = array_merge($json, getSystemDateAndTime($wsdl, $location));
        break;
        
        
    case 'getNTPInformation':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $json = array_merge($json, getNTPInformation($wsdl, $location));
        break;
    
    
    case 'setNTPFromDHCP':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        $device->setNTP(array('FromDHCP' => $post['Data'], 'NTPFromDHCP' => array(array('IPv4Address' => '132.163.4.102'))));
        $json = array_merge($json, getNTPInformation($wsdl, $location));
        break;
    
    
    case 'setNTPInformation':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        $device->setNTP($post['Data']);
        $json = array_merge($json, getNTPInformation($wsdl, $location));
        break;
    
    
    case 'getnetworkinterfaces':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $json = array_merge($json, getNetworkInterfaces($wsdl, $location));
        break;
    
    
    case 'setNetworkInterfaces':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        $device->setNetworkInterfaces($post['Data']);
        $json = array_merge($json, getNetworkInterfaces($wsdl, $location));
        break;
    
    
    case 'setSystemFactoryDefault':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        $device->setSystemFactoryDefault(array('FactoryDefault' => $post['Data']));
        break;
    
    
    case 'systemReboot':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        $device->systemReboot();
        break;
    
    
    case 'test':
        $wsdl = $includePath . 'onvif/ver10/device/wsdl/devicemgmt.wsdl';
        $location = $post['XAddrs'];
        $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
        $info = $device->getNetworkProtocols();
//$device->setSystemFactoryDefault(array('FactoryDefault' => 'Hard'));

        error_log(var_export($info, true));
        error_log($device->getSoapClient()->__getLastRequest());
        error_log($device->getSoapClient()->__getLastResponse());
        
        $json['info'] = (array)$info;
        break;
    
    
    default:
        $errors[] = 'Unknown method (' . $method . ').';
}




$json['errors'] = $errors;
$json['messages'] = $messages;

// make sure JSON is not cached
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

echo json_encode($json);
exit;





function getNetworkInterfaces($wsdl, $location) {
    $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
    $networkInterfaces = \NoCon\ONVIF\Device::objectToArray($device->getNetworkInterfaces()->NetworkInterfaces);
    
    // force into indexed array
    if ( !is_numeric(key($networkInterfaces)) ) {
        $networkInterfaces = array($networkInterfaces);
    }
    
    foreach ( $networkInterfaces as &$interface ) {
        if ( isset($interface['IPv4']['Config']['Manual']) && !is_numeric(key($interface['IPv4']['Config']['Manual'])) ) {
            $interface['IPv4']['Config']['Manual'] = array($interface['IPv4']['Config']['Manual']);
        }
    }
    
    return array('NetworkInterfaces' => $networkInterfaces);
}


function getNTPInformation($wsdl, $location) {
    $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
    $ntpInformation = \NoCon\ONVIF\Device::objectToArray($device->getNTP()->NTPInformation);
    
    // force into indexed arrays
    if ( isset($ntpInformation['NTPManual']) && !is_numeric(key($ntpInformation['NTPManual'])) ) {
        $ntpInformation['NTPManual'] = array($ntpInformation['NTPManual']);
    }
    if ( isset($ntpInformation['NTPFromDHCP']) && !is_numeric(key($ntpInformation['NTPFromDHCP'])) ) {
        $ntpInformation['NTPFromDHCP'] = array($ntpInformation['NTPFromDHCP']);
    }
    
    return array('NTPInformation' => $ntpInformation);
}


function getSystemDateAndTime($wsdl, $location) {
    $device = new \NoCon\ONVIF\Device(new \NoCon\ONVIF\Client($wsdl, array('trace' => true, 'location' => $location)));
    $response = $device->getSystemDateAndTime();
    return \NoCon\ONVIF\Device::objectToArray($response);
    
    $systemDateAndTime = \NoCon\ONVIF\Device::objectToArray($response->SystemDateAndTime);
    return array('SystemDateAndTime' => $systemDateAndTime);
}




function fixTimeZone($timezone) {
    return preg_replace('|^([A-Za-z]+)([0-9]+)$|', '$1+$2', $timezone);
}

