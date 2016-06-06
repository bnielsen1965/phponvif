<div class="tab" ng-class="currentTab==='other' ? 'currenttab' : ''">
    <button type="button" ng-click="getWsdlUrl()">Get WSDL URL</button> 
    <button type="button" ng-click="getServices()">Get Services</button> 
    <button type="button" ng-click="getCapabilities()">Get Capabilities</button>
    <button type="button" ng-click="getHostname()">Get Hostname</button>
    <button type="button" ng-click="getNetworkProtocols()">Get Network Protocols</button>
    <button type="button" ng-click="getSystemUris()">Get System Uris</button>
    <button type="button" ng-click="getSystemSupportInformation()">Get System Support Info</button>
    <button type="button" ng-click="getUsers()">Get Users</button>
    <br>
    <pre>{{other | json}}</pre>
</div>