<?php
 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

// Get date time formats
if (!function_exists('getDateTimeFormats')) {
    function getDateTimeFormats()
    {
        return [
            // Standard International Formats
            'Y-m-d H:i'  => '2025-03-03 14:30 (ISO 8601)',
            'Y-m-d H:i'    => '2025-03-03 14:30 (ISO 8601, No Seconds)',
            'Y/m/d H:i'  => '2025/03/03 14:30 (Japan, Korea)',
            'Y/m/d H:i'    => '2025/03/03 14:30 (Japan, Korea, No Seconds)',
            'Y.m.d H:i'  => '2025.03.03 14:30 (Alternative International)',
            
            // European Formats
            'd-m-Y H:i'    => '03-03-2025 14:30 (Germany, France, Italy)',
            'd/m/Y H:i'    => '03/03/2025 14:30 (Europe, Latin America)',
            'd.m.Y H:i'    => '03.03.2025 14:30 (Germany, Russia)',
            'd M Y H:i'    => '03 Mar 2025 14:30 (UK, India - 24-hour)',
            'd M Y h:i A'  => '03 Mar 2025 02:30 PM (UK, India - 12-hour)',

            // North & South America
            'm/d/Y h:i A'  => '03/03/2025 02:30 PM (USA, Canada)',
            'm-d-Y h:i A'  => '03-03-2025 02:30 PM (Alternative USA)',
            'M d, Y h:i A' => 'Mar 03, 2025 02:30 PM (Common format, North America)',
            'd-m-Y H:i'    => '03-03-2025 14:30 (Brazil, Argentina)',

            // Asia & Middle East
            'd/m/Y H:i'  => '03/03/2025 14:30 (India, Pakistan, UAE)',
            'Y-m-d g:i A'  => '2025-03-03 2:30 PM (China, South Korea, Japan - 12-hour)',
            'Y-m-d H:i'  => '2025-03-03 14:30 (Asia - 24-hour)',
            
            // Full Date & Time Formats
            'l, d F Y h:i A' => 'Monday, 03 March 2025 02:30 PM (Full Date & Time)',
            'l, d M Y H:i' => 'Monday, 03 Mar 2025 14:30 (Full Date, 24-hour)',
            'D, d M Y h:i A' => 'Mon, 03 Mar 2025 02:30 PM (Short Weekday, 12-hour)',
            'D, d M Y H:i' => 'Mon, 03 Mar 2025 14:30 (Short Weekday, 24-hour)',
        ];
    }
}

// Get date time formats
if (!function_exists('formatDateForUser')) {
    /**
     * Format a date based on the user's locale and timezone.
     *
     * @param string|\DateTimeInterface|null $date The date to format.
     * @param string $format The format type: 'full', 'short', 'time', 'datetime'.
     * @return string|null
     */
    function formatDateForUser($date, $format = 'full')
    {
        // Get application timezone
        $configs = DB::table('config')->get();

        if (!$date) {
            return null;
        }

        // Get user locale (default to 'en' if not set)
        $locale = App::getLocale() ?? 'en';

        // Get user timezone (default to 'UTC' if not set)
        $timezone = $configs[2]->config_value ?? 'UTC';

        // Convert date to Carbon instance
        $carbonDate = Carbon::parse($date)->setTimezone($timezone);
        $carbonDate->locale($locale); // Set locale for translation

        return $carbonDate->translatedFormat($configs[75]->config_value ?? 'M d, Y h:i A');
    }
}
