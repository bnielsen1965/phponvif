<div class="tab" ng-class="currentTab==='systemdateandtime' ? 'currenttab' : ''">
    <table class="details">
        <tr><th>Parameter</th><th>Value</th></tr>
        <tr>
            <td>DateTimeType</td>
            <td>
                <select ng-model="systemDateAndTime['DateTimeType']">
                    <option value="Manual">Manual</option>
                    <option value="NTP">NTP</option>
                </select>
                <button type="button" ng-click="setDateTimeType()">Set</button>
            </td>
        </tr>
        <tr>
            <td>DaylightSavings</td>
            <td>
                <select ng-model="systemDateAndTime['DaylightSavings']" ng-options="o for o in [false, true]"></select>
                <button type="button" ng-click="setDaylightSavings()">Set</button>
            </td>
        </tr>
        <tr>
            <td>TZ</td>
            <td>
                <select ng-model="systemDateAndTime['TimeZone']['TZ']" ng-options="o for o in utcOptions()"></select>
                <button type="button" ng-click="setTimeZone()">Set</button>
                <span class="smallinfo">* POSIX timezones may be opposite of what you might expect.</span>
            </td>
        </tr>
        <tr>
            <td>LocalDateTime</td>
            <td>{{systemDateAndTime['LocalDateTimeString']}}</td>
        </tr>
        <tr>
            <td>UTCDateTime</td>
            <td>
                <input type="text" ng-model="systemDateAndTime['UTCDateTimeString']">
                <button type="button" ng-click="adjustDateTime()">Adjust</button>
                <button type="button" ng-click="setCurrentDateTime()">Set Current</button>
            </td>
        </tr>
    </table>
</div>