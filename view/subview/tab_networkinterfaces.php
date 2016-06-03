<div class="tab" ng-class="currentTab==='networkinterfaces' ? 'currenttab' : ''">
    <table class="details">
        <tr ng-repeat="interface in networkInterfaces">
            <td>
                {{interface.token}}<br>
                Enabled : {{interface.Enabled}}
            </td>
            <td>
                IPv4:<br>
                <table class="details">
                    <tr>
                        <td>Enabled</td>
                        <td>
                            <select ng-model="interface.IPv4.Enabled" ng-options="o for o in [false, true]"></select>
                        </td>
                    </tr>
                    <tr>
                        <td>DHCP</td>
                        <td>
                            <select ng-model="interface.IPv4.Config.DHCP" ng-options="o for o in [false, true]"></select>
                        </td>
                    </tr>
                    <tr>
                        <td>FromDHCP</td>
                        <td>
                            <span ng-if="interface.IPv4.Config.FromDHCP">
                                {{interface.IPv4.Config.FromDHCP.Address}} / {{interface.IPv4.Config.FromDHCP.PrefixLength}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>IPv4Manual</td>
                        <td>
                            <span ng-repeat="manual in interface.IPv4.Config.Manual">
                                <input type="text" ng-model="manual.Address"> / <input class="prefixlength" type="text" ng-model="manual.PrefixLength">
                                <br>
                            </span>
                            <button type="button" ng-click="newIPv4Manual($index)">[+]</button> 
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="button" ng-click="setNetworkInterfaces($index)">Set</button>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                Info:<br>
                <table class="details">
                    <tr ng-repeat="(key, value) in interface.Info">
                        <td>{{key}}</td><td>{{value}}</td>
                    </tr>
                </table>
             </td>
        </tr>
    </table>
    {{networkInterfaces}}
</div>