<div class="tab" ng-class="currentTab==='details' ? 'currenttab' : ''">
    <table class="details">
        <tr><th>Parameter</th><th>Value</th></tr>
        <tr ng-repeat="detail in showDetails"><td><label>{{detail}}</label></td><td>{{device[detail]}}</td></tr>
    </table>
</div>