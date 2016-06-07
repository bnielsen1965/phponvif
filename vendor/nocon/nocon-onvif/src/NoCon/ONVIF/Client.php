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
 * NoCon\ONVIF\Client class provides basic SOAP messaging.
 * 
 * @author Bryan Nielsen <bnielsen1965@gmail.com>
 * @copyright (c) 2015, Bryan Nielsen
 */
class Client extends \SoapClient {
    
    private $nonce;
    private $timestamp;
    private $password;
    private $username;

    /**
     * Create an instance of the client using the provided WSDL.
     * 
     * @param string $wsdl Path to the ONVIF WSDL file.
     * @param mixed $options Array of options for SoapClient.
     */
    public function __construct($wsdl, $options = array()) {
        parent::__construct($wsdl, $options);
    }
    
    
    /**
     * Set the authentication parameters to be used in messages that require
     * an authentication header in the SOAP message.
     * 
     * @param string $nonce The authentication nonce value.
     * @param string $timestamp The authentication timestamp.
     * @param string $password The authentication password.
     * @param string $username The authentication username.
     */
    public function setAuth($nonce, $timestamp, $password, $username) {
        $this->nonce = $nonce;
        $this->timestamp = $timestamp;
        $this->password = $password;
        $this->username = $username;
    }

    
    /**
     * Execute a SOAP call.
     * 
     * @param type $functionName
     * @param type $arguments
     * @param type $options
     * @param type $inputHeaders
     * @param type $outputHeaders
     * @return type
     */
    public function __soapCall($functionName, $arguments, $options = null, $inputHeaders = null, &$outputHeaders = null) {
        switch ( $functionName ) {
            case 'CreateUsers':
            case 'DeleteUsers':
            case 'GetAccessPolicy':
                $response = parent::__soapCall($functionName, $arguments, $options, $this->generateWSSecurityHeader($this->nonce, $this->timestamp, $this->password, $this->username));
                break;
            
            default:
                $response = parent::__soapCall($functionName, $arguments, $options, $inputHeaders, $outputHeaders);
        }
        return $response;
    }
    
    
    /**
     * Generate a password digest from the provided authentication values.
     * 
     * @param string $nonce
     * @param string $timestamp
     * @param string $password
     * @return string The password digest.
     */
    public function generatePasswordDigest($nonce, $timestamp, $password) {
        return base64_encode(pack('H*', 
                sha1(base64_decode($nonce) . pack('a*', $timestamp) . pack('a*', $password))
            ));
    }
    
    
    /**
     * Generate a WSSecurity header for the SOAP message from the provided credentials.
     * 
     * @param string $nonce
     * @param string $timestamp
     * @param string $password
     * @param string $username 
     * @return \SoapHeader The security header.
     */
    private function generateWSSecurityHeader($nonce, $timestamp, $password, $username) {
        $wssNamespace = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
        $wsuNamespace = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd";
        $security = new \SoapVar(
            array(new \SoapVar(
                array(
                    new \SoapVar($username, XSD_STRING, null, null, 'Username', $wssNamespace),
                    new \SoapVar(self::generatePasswordDigest($nonce, $timestamp, $password), XSD_STRING, null, null, 'Password', $wssNamespace),
                    new \SoapVar($nonce, XSD_STRING, null, null, 'Nonce', $wssNamespace),
                    new \SoapVar($timestamp, XSD_STRING, null, null, 'Created', $wsuNamespace)
                ), 
                SOAP_ENC_OBJECT,
                null, 
                null, 
                'UsernameToken', 
                $wssNamespace
            )), 
            SOAP_ENC_OBJECT
        );
        
        return new \SoapHeader($wssNamespace, 'Security', $security, true);
    }
}
