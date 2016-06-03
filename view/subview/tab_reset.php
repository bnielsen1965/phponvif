<div class="tab" ng-class="currentTab==='reset' ? 'currenttab' : ''">
    <button type="button" ng-click="reboot()">Reboot</button> 
    <button type="button" ng-click="reset('Soft')">Reset Soft</button> 
    <button type="button" ng-click="reset('Hard')">Reset Hard</button> 
</div>