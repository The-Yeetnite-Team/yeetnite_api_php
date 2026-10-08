<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';
require_once 'lib/season_utils.php';

header('Content-Type: application/json');

$SERVER_TIME = current_zulu_time();
$CREATED_LAST_LOGIN = $database->select(array('created', 'lastLogin'), 'users', "WHERE username = '{$_GET['accountId']}'")[0];
$RVN = intval($_GET['rvn']) ?? -1;

switch ($_GET['profileId']) {
    case 'athena':
        header("X-LiteSpeed-Tag: queryProfileAthena/{$_GET['accountId']}");
        switch ($RVN) {
            case -1:
                $athena_profile = $cache_provider->get('fortnite_api_game_v2_profile_athena');
                $version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
                $locker_data = $database->select(
                    array(
                        'banner_icon',
                        'banner_color',
                        'favorite_victorypose',
                        'favorite_consumableemote',
                        'favorite_callingcard',
                        'favorite_character',
                        'favorite_spray',
                        'favorite_loadingscreen',
                        'favorite_hat',
                        'favorite_battlebus',
                        'favorite_mapmarker',
                        'favorite_vehicledeco',
                        'favorite_backpack',
                        'favorite_dance',
                        'favorite_skydivecontrail',
                        'favorite_pickaxe',
                        'favorite_glider',
                        'favorite_musicpack',
                        'favorite_itemwrap'
                    ),
                    'locker',
                    "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')"
                )[0];

                $athena_profile = strtr(
                    $athena_profile,
                    array(
                        '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
                        '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
                        '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
                        '{{SEASON_NUM}}' => $version_info['season'],
                        '"{{BANNER_ICON}}"' => '"' . $locker_data['banner_icon'] . '"',
                        '"{{BANNER_COLOR}}"' => '"' . $locker_data['banner_color'] . '"',
                        '"{{FAVORITE_VICTORYPOSE}}"' => '"' . $locker_data['favorite_victorypose'] . '"',
                        '"{{FAVORITE_CONSUMABLEEMOTE}}"' => '"' . $locker_data['favorite_consumableemote'] . '"',
                        '"{{FAVORITE_CALLINGCARD}}"' => '"' . $locker_data['favorite_callingcard'] . '"',
                        '"{{FAVORITE_CHARACTER}}"' => '"' . $locker_data['favorite_character'] . '"',
                        '{{FAVORITE_SPRAY}}' => $locker_data['favorite_spray'] ?: '[]',
                        '"{{FAVORITE_LOADINGSCREEN}}"' => '"' . $locker_data['favorite_loadingscreen'] . '"',
                        '"{{FAVORITE_HAT}}"' => '"' . $locker_data['favorite_hat'] . '"',
                        '"{{FAVORITE_BATTLEBUS}}"' => '"' . $locker_data['favorite_battlebus'] . '"',
                        '"{{FAVORITE_MAPMARKER}}"' => '"' . $locker_data['favorite_mapmarker'] . '"',
                        '"{{FAVORITE_VEHICLEDECO}}"' => '"' . $locker_data['favorite_vehicledeco'] . '"',
                        '"{{FAVORITE_BACKPACK}}"' => '"' . $locker_data['favorite_backpack'] . '"',
                        '{{FAVORITE_DANCE}}' => $locker_data['favorite_dance'] ?: '[]',
                        '"{{FAVORITE_SKYDIVECONTRAIL}}"' => '"' . $locker_data['favorite_skydivecontrail'] . '"',
                        '"{{FAVORITE_PICKAXE}}"' => '"' . $locker_data['favorite_pickaxe'] . '"',
                        '"{{FAVORITE_GLIDER}}"' => '"' . $locker_data['favorite_glider'] . '"',
                        '"{{FAVORITE_MUSICPACK}}"' => '"' . $locker_data['favorite_musicpack'] . '"',
                        '{{FAVORITE_ITEMWRAPS}}' => $locker_data['favorite_itemwrap'] ?: '[]',
                        '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
                    )
                );

                echo $athena_profile;
                break;
            default:
                echo json_encode(array(
                    'profileRevision' => 17306,
                    'profileId' => 'athena',
                    'profileChangesBaseRevision' => 17306,
                    'profileChanges' => array(),
                    'profileCommandRevision' => $RVN,
                    'serverTime' => $SERVER_TIME,
                    'responseVersion' => 1
                ));
                break;
        }
        break;
    case 'common_core':
        switch ($RVN) {
            case -1:
                $common_core = $cache_provider->get('fortnite_api_game_v2_profile_common_core');
                $common_core = strtr($common_core, array(
                    '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
                    '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
                    '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
                    '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
                ));
                echo $common_core;
                break;
            default:
                echo json_encode(array(
                    'profileRevision' => 2409,
                    'profileId' => 'common_core',
                    'profileChangesBaseRevision' => 2409,
                    'profileChanges' => [],
                    'profileCommandRevision' => $RVN,
                    'serverTime' => $SERVER_TIME,
                    'responseVersion' => 1
                ));
                break;
        }
        break;
    case 'common_public':
        $banner_info = $database->select(array('banner_icon', 'banner_color'), 'locker', "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')")[0];

        $common_public = $cache_provider->get('fortnite_api_game_v2_profile_common_public');
        $common_public = strtr($common_public, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '"{{BANNER_COLOR}}"' => '"' . $banner_info['banner_color'] . '"',
            '"{{BANNER_ICON}}"' => '"' . $banner_info['banner_icon'] . '"',
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));

        echo $common_public;
        break;
    case 'profile0':
        $profile0 = $cache_provider->get('fortnite_api_game_v2_profile_profile0');
        $profile0 = strtr($profile0, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));
        echo $profile0;
        break;
    case 'creative':
        echo json_encode(array(
            'profileRevision' => 203,
            'profileId' => 'creative',
            'profileChangesBaseRevision' => 203,
            'profileChanges' => array(
                array(
                    'changeType' => 'fullProfileUpdate',
                    'profile' => array(
                        '_id' => $_GET['accountId'],
                        'created' => $CREATED_LAST_LOGIN['created'],
                        'updated' => $CREATED_LAST_LOGIN['lastLogin'],
                        'rvn' => 203,
                        'wipeNumber' => 11,
                        'accountId' => $_GET['accountId'],
                        'profileId' => 'creative',
                        'version' => 'ensure_project_ids_october_2021',
                        'items' => new stdClass(),
                        'stats' => array(
                            'attributes' => new stdClass()
                        ),
                        'commandRevision' => 197
                    )
                )
            ),
            'profileCommandRevision' => 197,
            'serverTime' => $SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'collection_book_people0':
        $collection_book_people0 = $cache_provider->get('fortnite_api_game_v2_profile_collection_book_people0');
        $collection_book_people0 = strtr($collection_book_people0, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));
        echo $collection_book_people0;
        break;
    case 'collection_book_schematics0':
        $collection_book_schematics0 = $cache_provider->get('fortnite_api_game_v2_profile_collection_book_schematics0');
        $collection_book_schematics0 = strtr($collection_book_schematics0, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));
        echo $collection_book_schematics0;
        break;
    case 'campaign':
        echo json_encode(array(
            'profileRevision' => $RVN,
            'profileId' => 'campaign',
            'profileChangesBaseRevision' => $RVN,
            'profileChanges' => array(),
            'profileCommandRevision' => $RVN - 10,
            'serverTime' => $SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'metadata':
        $metadata = $cache_provider->get('fortnite_api_game_v2_profile_metadata');
        $metadata = strtr($metadata, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));
        echo $metadata;
        break;
    case 'theater0':
        $theater0 = $cache_provider->get('fortnite_api_game_v2_profile_theater0');
        $theater0 = strtr($theater0, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));
        echo $theater0;
        break;
    case 'outpost0':
        echo json_encode(array(
            'profileRevision' => 1,
            'profileId' => 'outpost0',
            'profileChangesBaseRevision' => 1,
            'profileChanges' => array(
                array(
                    'changeType' => 'fullProfileUpdate',
                    'profile' => array(
                        '_id' => 'Yeetnite',
                        'created' => $CREATED_LAST_LOGIN['created'],
                        'updated' => $CREATED_LAST_LOGIN['lastLogin'],
                        'rvn' => 1,
                        'wipeNumber' => 1,
                        'accountId' => $_GET['accountId'],
                        'profileId' => 'outpost0',
                        'version' => 'no_version',
                        'items' => new stdClass(),
                        'stats' => array(
                            'attributes' => array(
                                'inventory_limit_bonus' => 0
                            )
                        ),
                        'commandRevision' => 0
                    )
                )
            ),
            'profileCommandRevision' => 0,
            'serverTime' => $SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'collections':
        $collections = $cache_provider->get('fortnite_api_game_v2_profile_collections');
        $version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
        $collections = strtr($collections, array(
            '"{{CREATED}}"' => '"' . $CREATED_LAST_LOGIN['created'] . '"',
            '"{{UPDATED}}"' => '"' . $CREATED_LAST_LOGIN['lastLogin'] . '"',
            '"{{ACCOUNT_ID}}"' => '"' . $_GET['accountId'] . '"',
            '{{SEASON_NUM}}' => $version_info['season'],
            '"{{SERVER_TIME}}"' => '"' . $SERVER_TIME . '"'
        ));
        echo $collections;
        break;
}
