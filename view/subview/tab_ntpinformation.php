<div class="tab" ng-class="currentTab==='ntpinformation' ? 'currenttab' : ''">
    <table class="details">
        <tr><th>Parameter</th><th>Value</th></tr>
        <tr>
            <td>FromDHCP</td>
            <td>
                <select ng-model="ntpInformation['FromDHCP']" ng-options="o for o in [false, true]"></select>
                <button type="button" ng-click="setNTPFromDHCP()">Set</button>
            </td>
        </tr>
        <tr>
            <td><label>NTPFromDHCP</label></td>
            <td><span ng-repeat="ntp in ntpInformation['NTPFromDHCP']">{{ntp.Type}} : {{ntp.DNSname}}{{ntp.IPv4Address}}{{ntp.IPv6Address}}<br></span></td>
        </tr>
        <tr>
            <td><label>NTPManual</label></td>
            <td>
                <span ng-repeat="ntp in ntpInformation['NTPManual']">
                    <select ng-model="ntp.Type">
                        <option value="DNS">DNS</option>
                        <option value="IPv4">IPv4</option>
                        <option value="IPv6">IPv6</option>
                    </select> :
                    <input ng-if="ntp.Type=='DNS'" ng-model="ntp.DNSname">
                    <input ng-if="ntp.Type=='IPv4'" ng-model="ntp.IPv4Address">
                    <input ng-if="ntp.Type=='IPv6'" ng-model="ntp.IPv6Address">
                    <br>
                </span>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="button" ng-click="newNTPManual()">[+]</button> 
                <button type="button" ng-click="setNTPInformation()">Set NTP</button>
            </td>
        </tr>
    </table>
</div>