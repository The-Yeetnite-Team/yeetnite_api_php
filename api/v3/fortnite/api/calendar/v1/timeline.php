<?php
require_once 'cache_provider.php';
require_once 'lib/season_utils.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');
header('X-Litespeed-Cache-Control: no-store'); // OLS doesn't differentiate by headers

$version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
$event_flag_season = "EventFlag.Season{$version_info['season']}";
$current_time = current_zulu_time();
$timeline = $cache_provider->get('fortnite_api_calendar_v1_timeline');

$timeline = strtr($timeline, array(
    '"{{CURRENT_TIME}}"' => '"' . $current_time . '"',
    '{{SEASON_NUM}}' => $version_info['season'],
    '"{{SEASON_TEMPLATE_ID}}"' => '"AthenaSeason:athenaseason' . $version_info['season'] . '"',
    '"{{EVENT_TYPE_SEASON}}"' => '"' . $event_flag_season . '"',
    '"{{EVENT_TYPE_LOBBY}}"' => '"EventFlag.' . $version_info['lobby'] . '"'
));

echo $timeline;
