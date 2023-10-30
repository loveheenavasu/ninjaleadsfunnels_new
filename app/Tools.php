<?php

namespace App;

class Tools
{
    const REFERER = 'referer';
    const SYNDICATION = 'syndication';
    const NINJA_FUNNELS = 'ninja_funnels';
    const User_Management = 'user_management';
    const DRIP_FEED = 'drip_feed';
    const CUTTER = 'cutter';
    const EVENT_CALENDER = 'event_calender';
    const LEAD_VALIDATOR = 'leadvalidator';


    public static function all(): array
    {
        return [
            self::REFERER,
            self::SYNDICATION,
            self::NINJA_FUNNELS,
            self::User_Management,
            self::CUTTER,
            self::EVENT_CALENDER
        ];
    }

    public static function current(): string
    {
        return session()->get('selected_tool', 'ninja_funnels');
    }

    public static function isCurrent($tool): bool
    {
        return in_array(static::current(), (array)$tool);
    }

    public static function switch(string $tool): void
    {
        session()->put('selected_tool', $tool);
    }
}
