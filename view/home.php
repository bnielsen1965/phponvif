<?php namespace NoCon\Framework; // namespace needed to access classes ?>

<div>
    <div class="listbox-container">
        <div class="infobox">
            <img src="<?php echo Router::$ARGS['IMG_URL']; ?>ajax-loader.gif" class="busygif" ng-class="busy ? 'showbusy' : ''">
            <span class="infotext">{{infotext}}</span>
        </div>
        
        <div class="listbox">
            <table class="details">
                <tr><th>Device List</th></tr>
                <tr ng-repeat="item in devices">
                    <td><a class="device" ng-click="selectDevice(item)">{{item.Model}}</a></td>
                </tr>
            </table>
            <br>
            <button type="button" ng-click="discoverCameras()">Discover</button>
        </div>
        
        <div class="listbox">
            <span class="devicetitle">Device: {{device.Model}} : {{device.HardwareId}}</span>
            <!-- tabs shown above the tab container -->
            <ul class="tabs">
                <li ng-repeat="tab in tabs" ng-class="tab.name==currentTab ? 'currenttab' : ''"><a ng-click="selectTab(tab.name)">{{tab.title}}</a></li>
            </ul>
            
            <!-- tab content container -->
            <div class="tabcontainer">
                <?php Router::includeView('subview/tab_details');?>
                <?php Router::includeView('subview/tab_systemdateandtime');?>
                <?php Router::includeView('subview/tab_ntpinformation');?>
                <?php Router::includeView('subview/tab_networkinterfaces');?>
                <?php Router::includeView('subview/tab_reset');?>
            </div>
        </div>
    </div>
    
    <button name="test" value="1" type="button" ng-click="test()">Test</button>
</div>
