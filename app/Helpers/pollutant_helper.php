<?php

if (!function_exists('getPollutantClass')) {
    /**
     * Returns CSS class based on AQI value
     * 
     * @param int $aqi Air Quality Index value
     * @return string CSS class name
     */
    function getPollutantClass($aqi) {
        if ($aqi <= 50) return 'online';    // Good
        if ($aqi <= 100) return 'warning';  // Moderate
        return 'critical';                   // Poor
    }
}