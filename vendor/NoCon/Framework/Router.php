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

namespace NoCon\Framework;

/**
 * Router class, routes incoming requests.
 * 
 * @author Bryan Nielsen <bnielsen1965@gmail.com>
 * @copyright (c) 2014, Bryan Nielsen
 * 
 */
class Router {
    /**
     * This is the name of the current view the router is loading.
     * 
     * @var string The name of the current view.
     */
    private static $VIEW;
    
    /**
     * The request parameters passed in the URL. The 0 element is always the
     * view name that is requested and may be an empty string when requesting
     * the default view.
     * 
     * @var array Array of values passed in the URL
     */
    private static $PARAMS;
    
    /**
     * In some cases it is necessary to disable all views, i.e. when the preprocess
     * provides the output in an API call. This flag is set when views should be
     * disabled.
     * 
     * @var boolean Flag used to disable all views.
     */
    private static $DISABLE_VIEW;
    
    /**
     * A static helper variable that can be used to store arguments that will be
     * shared across the various view components that may be loaded by the main view.
     * 
     * @var type Custom arguments to share across view components.
     */
    public static $ARGS;
    
    
    /**
     * Router initialization function. Sets up the static variable values and
     * loads any values passed in the URL.
     */
    public static function init() {
        // initialize empty values
        static::$PARAMS = array();
        static::$VIEW = Config::get('application', 'defaultView');
        static::$ARGS = array();
        
        // collect any passed URL parameters
        if (isset($_GET['parameters'])) {
            // extract all parameters into array
            static::$PARAMS = explode('/', $_GET['parameters']);
            
            // if the first parameter is set then use as view
            if (!empty(static::$PARAMS[0])) {
                // store sanitized view name
                static::$VIEW = static::sanitizeString(static::$PARAMS[0]);
            }
        }
    }
    
    
    /**
     * A function to sanitize an string for use in URLs, filenames, paths, etc.
     * 
     * @param string $string The string to be sanitized.
     * 
     * @return string The sanitized string.
     */
    public static function sanitizeString($string) {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '-', $string);
    }
    
    
    /**
     * Get the current set of router parameters.
     * 
     * @return mixed The router parameters.
     */
    public static function getParameters() {
        return static::$PARAMS;
    }
    
    
    /**
     * Get the current view name.
     * 
     * @return string The view name determined by the router.
     */
    public static function getViewName() {
        return static::$VIEW;
    }
    
    
    /**
     * Load the current or specified view using the optional preprocess and layout if provided.
     * 
     * @param string $preprocessFile The fully qualified path to the preprocess script, null 
     * to use the default preprocess script, or false if no preprocess is to be used.
     * @param string $layoutFile The fully qualified path to the layout script, null 
     * to use the default layout script, or false if no layout is to be used.
     * @param string $view The view to load.
     */
    public static function loadView($preprocessFile = null, $layoutFile = null, $view = null) {
        // run preprocess
        if (false !== $preprocessFile) {
            if (is_null($preprocessFile)) {
                // using default preprocess
                include Config::get('framework', 'preprocessPath') . Config::get('application', 'defaultPreprocess') . '.php';
            }
            elseif (!empty($preprocessFile) && file_exists($preprocessFile)) {
                // using the specified preprocess
                include Config::get('framework', 'preprocessPath') . $preprocessFile . '.php';
            }
            else {
                // something has gone awry
                error_log('preprocess not found');
            }
            
            // check if views are disabled
            if ( static::$DISABLE_VIEW ) {
                return true;
            }
        }
        
        // use layout
        if (false !== $layoutFile) {
            if (is_null($layoutFile)) {
                // using default layout
                include Config::get('framework', 'layoutPath') . Config::get('application', 'defaultLayout') . '.php';
            }
            elseif (!empty($layoutFile) && file_exists($layoutFile)) {
                // using the provided layout
                include Config::get('framework', 'layoutPath') . $layoutFile . '.php';
            }
            else {
                // something has gone awry
                error_log('layout not found');
            }
        }
        else {
            // no layout, just include the view
            return static::includeView($view);
        }
        
        // failure
        return false;
    }
    
    
    /**
     * Include the specified view.
     * 
     * @param string $view The view to load. This can be the simple view name,
     * a fully qualified path to the view file, or null. If the view name is provided
     * then the view file is included from the defined view path. If a fully qualified
     * path is provided then that file is included as a view. And if null is provided
     * then the current Router view is included.
     */
    public static function includeView($view = null) {
        if (!empty($view) && file_exists($view)) {
            // load view from fully qualified path
            include $view;

            // success
            return true;
        }
        elseif (!empty($view)) {
            // determine path from view name
            $view = Config::get('framework', 'viewPath') . $view . '.php';

            if (file_exists($view)) {
                // load view from name
                include $view;

                // success
                return true;
            }
            else {
                // something has gone awry
                include Config::get('framework', 'viewPath') . '404.php';
                error_log('specified view not found: ' . $view);
            }
        }
        else {
            // using the current view
            $view = Config::get('framework', 'viewPath') . static::$VIEW . '.php';

            if (file_exists($view)) {
                // load current view
                include $view;

                // success
                return true;
            }
            else {
                // something has gone awry
                header("HTTP/1.0 404 Not Found");
                include Config::get('framework', 'viewPath') . '404.php';
                error_log('current view not found: ' . $view);
            }
        }

        // failure
        return false;
    }
    
    
    /**
     * Called to disable the view, i.e. when the preprocess has already provided
     * the content for cases such as an API call.
     */
    public static function disableView() {
        static::$DISABLE_VIEW = true;
    }
}

// initialize static class on load
Router::init();
