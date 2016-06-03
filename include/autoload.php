<?php
/*
* Copyright (C) 2014, 2015 Bryan Nielsen - All Rights Reserved
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

/**
 * Register the class autoloader function.
 */
spl_autoload_register(function ($class) {
    // split namespace and class name and get class name
    $classParts = explode('\\', $class);
    $class = end($classParts);
    
    // get the class path from the configuration settings
    $classPath = \NoCon\Framework\Config::get('framework', 'vendorPath');
    
    // check root path first
    if (file_exists($classPath . $class . '.php')) {
        // use class found in root path
        include $classPath . $class . '.php';
        return;
    }
    
    // if namespace provided then attempt namespace based path
    if (count($classParts) > 1) {
        $namespacePath = implode('/', $classParts);
        
        if (file_exists($classPath . $namespacePath . '.php')) {
            // use class found in root path
            include $classPath . $namespacePath . '.php';
            return;
        }
    }
});
