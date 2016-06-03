<?php
/*
* Copyright (C) 2016 Bryan Nielsen - All Rights Reserved
*
* Author: Bryan Nielsen <bnielsen1965@gmail.com>
*
*
* This file is part of the NoCon PHP application framework.
* NoCon is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* NoCon is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this application.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace NoCon\ONVIF;

/**
 * NoCon\ONVIF\Device class provides ONVIF device functions.
 * 
 * @author Bryan Nielsen <bnielsen1965@gmail.com>
 * @copyright (c) 2015, Bryan Nielsen
 */
class Device {
    
    /**
     * SOAP XML template used to generate WS Discovery requests. Note the [UUID]
     * in the template will be replaced with a new UUID value by the discovery method.
     */
    const WS_DISCOVERY_MESSAGE =  '<?xml version="1.0" encoding="UTF-8"?>
            <soapenv:Envelope xmlns:soapenv="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:d="http://schemas.xmlsoap.org/ws/2005/04/discovery" xmlns:dn="http://www.onvif.org/ver10/network/wsdl">
            <soapenv:Header>
            <wsa:MessageID>uuid:[UUID]</wsa:MessageID>
            <wsa:To mustUnderstand="true">urn:schemas-xmlsoap-org:ws:2005:04:discovery</wsa:To>
            <wsa:Action mustUnderstand="true">http://schemas.xmlsoap.org/ws/2005/04/discovery/Probe</wsa:Action>
            </soapenv:Header>
            <soapenv:Body><d:Probe><d:Types>dn:NetworkVideoTransmitter</d:Types></d:Probe></soapenv:Body>
            </soapenv:Envelope>';
    
    /**
     * The maximum number of seconds that will be allowed for the discovery request.
     */
    const WS_DISCOVERY_TIMEOUT = 10;
    
    /**
     * The multicast address to use in the socket for the discovery request.
     */
    const WS_DISCOVERY_MULTICAST_ADDRESS = '239.255.255.250';
    
    /**
     * The port that will be used in the socket for the discovdery request.
     */
    const WS_DISCOVERY_MULTICAST_PORT = 3702;
    
    
    /**
     *
     * @var \NoCon\ONVIF\Client The ONVIF SoapClient instance that is used to 
     * make SOAP calls.
     */
    private $soapClient;
    
    
    /**
     * Construct a Device instance.
     * 
     * @param \NoCon\ONVIF\Client $soapClient The ONVIF SoapClient instance that
     * is used to make SOAP calls.
     */
    public function __construct($soapClient) {
        $this->soapClient = $soapClient;
    }
    
    
    /**
     * Get the current SoapClient instance.
     * 
     * @return \NoCon\ONVIF\Client Get the current SoapClient instance.
     */
    public function getSoapClient() {
        return $this->soapClient;
    }
    
    
    /**
     * Set the authentication parameters to be used in methods that require
     * authentication.
     * 
     * @param string $nonce
     * @param string $timestamp
     * @param string $password
     * @param string $username
     */
    public function setAuth($nonce, $timestamp, $password, $username) {
        $this->getSoapClient()->setAuth($nonce, $timestamp, $password, $username);
    }
    
    
    /**
     * Reset to factory defaults, Hard or Soft.
     * 
     * Requires an associative array with the FactoryDefault value of Hard or Soft.
     * 
     * @param array $args An associative array with the FactoryDefault selection.
     * @return object The SOAP response.
     */
    public function setSystemFactoryDefault($args) {
        return $this->soapClient->__soapCall('SetSystemFactoryDefault', array($args));
    }
    
    
    /**
     * Request a system reboot of the device.
     * 
     * @return object The system reboot message.
     */
    public function systemReboot() {
        return $this->soapClient->__soapCall('SystemReboot', array());
    }
    
    
    /**
     * Get the wsdl URL as specified by the device.
     * 
     * @return object The SOAP response with the WsdlUrl value.
     */
    public function getWsdlUrl() {
        return $this->soapClient->__soapCall('GetWsdlUrl', array());
    }
    
    
    /**
     * Get device services details.
     * 
     * @param boolean $includeCapability Should capability be included in response.
     * @return object The SOAP response with the Service array.
     */
    public function getServices($includeCapability = true) {
        $args = array(
            'IncludeCapability' => $includeCapability
        );
        return $this->soapClient->__soapCall('GetServices', array($args));
    }
    
    
    /**
     * Get device service capabilities.
     * 
     * @return object The SOAP response with the Service capabilities array.
     */
    public function getServiceCapabilities() {
        return $this->soapClient->__soapCall('GetServiceCapabilities', array());
    }
    
    
    /**
     * Get device capabilities.
     * 
     * @return object The SOAP response with the Capabilities array.
     */
    public function getCapabilities() {
        return $this->soapClient->__soapCall('GetCapabilities', array());
    }
    
    
    /**
     * Get device date and time settings.
     * 
     * @return object The SOAP response with the SystemDateAndTime object.
     */
    public function getSystemDateAndTime() {
        return $this->soapClient->__soapCall('GetSystemDateAndTime', array());
    }
    
    
    public function setSystemDateAndTime($args) {
        return $this->soapClient->__soapCall('SetSystemDateAndTime', array($args));
    }
    
    
    public function getNTP() {
        return $this->soapClient->__soapCall('GetNTP', array());
    }
    
    
    public function setNTP($args) {
        return $this->soapClient->__soapCall('SetNTP', array($args));
    }
    
    
    /**
     * Get the device hostname settings.
     * 
     * @return object The SOAP response with the HostnameInformation object.
     */
    public function getHostname() {
        return $this->soapClient->__soapCall('GetHostname', array());
    }
    
    
    /**
     * Set a new hostname on the device.
     * 
     * @param string $name The new hostname.
     * @return object The SOAP response.
     */
    public function setHostname($name) {
        $args = array(
            'Name' => $name
        );
        return $this->soapClient->__soapCall('SetHostname', array($args));
    }
    
    
    /**
     * Get the device network interface details.
     * 
     * @return object The SOAP response with the NetworkInterfaces object.
     */
    public function getNetworkInterfaces() {
        return $this->soapClient->__soapCall('GetNetworkInterfaces', array());
    }
    
    
    /**
     * Set the network interface settings specified in the interface array.
     * 
     * The interface array should include an 'InterfaceToken' element with the
     * value set to the token of the interface to be adjusted and a 'NetworkInterface' 
     * element with the value set to an array of parameters to be set.
     * 
     * I.E. the follow parameters manuall sets an IPv4 address on interface eth0:
     * 
     * 'InterfaceToken' => 'eth0',
     * 'NetworkInterface' => array(
     *   'IPv4' => array(
     *     'Enabled' => true,
     *     'DHCP' => false,
     *     'Manual' => array(
     *       array(
     *         'Address' => '192.168.8.220',
     *         'PrefixLength' => '24'
     *       )
     *     )
     *   )
     * )
     * 
     * See the ONVIF devicemgmt.wsdl and onvif.xsd for a full list of parameters.
     * 
     * 
     * @param array $interface The NetworkInterface settings.
     * @return object The SOAP response.
     */
    public function setNetworkInterfaces($interface) {
        return $this->soapClient->__soapCall('SetNetworkInterfaces', array($interface));
    }
    
    
    /**
     * Get the device network protocol details.
     * 
     * @return object The SOAP response with the NetworkProtocols object.
     */
    public function getNetworkProtocols() {
        return $this->soapClient->__soapCall('GetNetworkProtocols', array());
    }
    
    
    public function setNetworkProtocols($protocols) {
        $args = array('NetworkProtocols' => $protocols);
        return $this->soapClient->__soapCall('SetNetworkProtocols', array($args));
    }
    
    
    public function getDeviceInformation() {
        return $this->soapClient->__soapCall('GetDeviceInformation', array());
    }
    
    
    public function getSystemUris() {
        return $this->soapClient->__soapCall('GetSystemUris', array());
    }
    
    
    public function getSystemBackup() {
        return $this->soapClient->__soapCall('GetSystemBackup', array());
    }
    
    
    public function getSystemLog($logType) {
        $args = array('LogType' => $logType);
        $response = $this->soapClient->__soapCall('GetSystemLog', array($args));
        var_export($response);
        return $response;
    }
    
    
    public function getSystemSupportInformation() {
        $response = $this->soapClient->__soapCall('GetSystemSupportInformation', array());
        return $response;
    }
    
    
    public function getRelayOutputs() {
        $response = $this->soapClient->__soapCall('GetRelayOutputs', array());
        var_export($response);
        return $response;
    }
    
    
    public function getAnalyticsEngines() {
        $response = $this->soapClient->__soapCall('GetAnalyticsEngines', array());
        var_export($response);
        return $response;
    }
    
    
    
    
    
    
    /**
     * Discover addresses of devices.
     * 
     * Broadcast a SOAP discovery message to discover ONVIF devices available on the network.
     * All method parameters are optional. Provide the number of timeout seconds for the
     * discovery process or pass null to use the default value. Provide a network interface IP
     * address or hostname for the discovery broadcast or pass null to accept the default of
     * all interfaces with the 0.0.0.0 address. Provide a custom SOAP discovery message or pass
     * null to accept the default SOAP message. When using a custom SOAP message be sure to 
     * include the [UUID] tag in the MessageID node that will be replaced with a generated uuid
     * for the message.
     * 
     * The return value is an array of associative arrays for each discovered device. Each 
     * associative array will contain an XAddrs element for the device ONVIF endpoint and
     * an IPAddrs element with the network address that provided the discovery response.
     * 
     * NOTE: PHP 5.4+ will use multicast while 5.3 and under will use broadcast. On systems
     * with PHP 5.3 or less and more than one network interface it is necessary to provide
     * the $bindIPs parameter with the address of all interfaces that should be used for
     * discovery. On systems with PHP 5.4+ you only need to specify the $bindIPs parameter
     * if the system has multiple network interfaces and you want the discover sent out on
     * a specific interface, otherwise it will multicast to all interfaces.
     * 
     * @param integer $timeout Optional number of seconds to to timeout the discovery process.
     * @param string $bindIPs Optional comma delimited list of interface ip addresses used to make the discovery request.
     * @param string $message Optional SOAP discovery message to use.
     * @return array An array of discovered addresses.
     */
    public static function discover($timeout = null, $bindIPs = null, $message = null) {
        // configure discovery parameters
        $discoveryTimeout = time() + (null === $timeout ? self::WS_DISCOVERY_TIMEOUT : $timeout);
        $discoveryBindIPs = (null === $bindIPs ? '0.0.0.0' : $bindIPs);
        $uuid = self::uuidV4();
        $discoveryMessage = str_replace('[UUID]', $uuid, (null === $message ? self::WS_DISCOVERY_MESSAGE : $message));
        $discoveryPort = self::WS_DISCOVERY_MULTICAST_PORT;
        
        // initialize the discovery list and socket
        $discoveryList = array();
        $bindIPList = explode(',', $discoveryBindIPs);
        
        foreach ( $bindIPList as $discoveryBindIp ) {
            $discoveryBindIp = trim($discoveryBindIp);
            if ( empty($discoveryBindIp) ) {
                continue;
            }
            
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
            
            if ( defined('IPPROTO_IP') && defined('MCAST_JOIN_GROUP') ) {
                socket_set_option($sock, IPPROTO_IP, MCAST_JOIN_GROUP, array('group' => self::WS_DISCOVERY_MULTICAST_ADDRESS));
            }
            else {
                socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);
            }
            
            socket_bind($sock, $discoveryBindIp, self::WS_DISCOVERY_MULTICAST_PORT);
            //socket_set_option($sock, IPPROTO_IP, MCAST_JOIN_GROUP, array('group' => self::WS_DISCOVERY_MULTICAST_ADDRESS));
            socket_sendto($sock, $discoveryMessage, strlen($discoveryMessage), 0, self::WS_DISCOVERY_MULTICAST_ADDRESS, self::WS_DISCOVERY_MULTICAST_PORT);

            // check for replies until timeout reached
            $response = $from = null;
            do {
                // receive responses while they are available and valid
                while (
                        false !== @socket_recvfrom($sock, $response, 9999, MSG_DONTWAIT, $from, $discoveryPort) && 
                        false !== ($xml = \DOMDocument::loadXML($response)) &&
                        self::relatesToMatch($uuid, $xml) ) {
                    // get the XAddrs matches and modify with the from IP address
                    $xAddrs = self::getProbeMatchXAddrs($xml);
                    $discoveryList = array_map(function($addrs) use ($from) {
                        return array(
                            'XAddrs' => $addrs,
                            'IPAddrs' => $from
                        );
                    }, $xAddrs);
                }
                
                // wait a moment for replies
                usleep(50000);
            } while ( time() < $discoveryTimeout );

            socket_close($sock);
        }
        
        return $discoveryList;
    }
    
    
    /**
     * Check an XML response message to see if it has a RelatesTo node with a UUID
     * value that matches the UUID of the original request method.
     * 
     * @param string $uuid The original message UUID.
     * @param \DOMDocument $xmlDOMDoc The XML response in a DOMDocument object.
     * @return boolean Returns true if the XML message contains a RelatesTo node with a matching UUID value.
     */
    public static function relatesToMatch($uuid, $xmlDOMDoc) {
        $relatesNodes = $xmlDOMDoc->getElementsByTagName('RelatesTo');
        foreach ( $relatesNodes as $node ) {
            if ( preg_match('|' . $uuid . '$|', $node->nodeValue) ) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Get an array of XAddrs values from the ProbeMatches in a discovery response
     * message.
     * 
     * @param \DOMDocument $xmlDOMDoc
     * @return array An array of XAddrs values from the ProbeMatches.
     */
    public static function getProbeMatchXAddrs($xmlDOMDoc) {
        $matches = array();
        $probeMatchNodes = $xmlDOMDoc->getElementsByTagName('ProbeMatch');
        foreach ( $probeMatchNodes as $node ) {
            $xAddrsNodes = $node->getElementsByTagName('XAddrs');
            foreach ( $xAddrsNodes as $addrsNode ) {
                $matches[] = $addrsNode->nodeValue;
            }
        }
        return $matches;
    }
    
    
    /**
     * Roger Stringer's UUID function, http://rogerstringer.com/2013/11/15/generate-uuids-php/
     * 
     * @return string A random uuid.
     */
    public static function uuidV4() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		mt_rand( 0, 0xffff ),
		mt_rand( 0, 0x0fff ) | 0x4000, // this sequence must start with 4
		mt_rand( 0, 0x3fff ) | 0x8000, // this sequence can start with 8, 9, A, or B
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
    }
    
    
    public static function objectToArray($object) {
        $array = array_merge(array(), (array)$object);
        
        foreach ($array as &$value) {
            if ( is_array($value) || is_object($value) ) {
                $value = self::objectToArray($value);
            }
        }
        
        return $array;
    }
}
