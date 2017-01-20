<?php
namespace MusicCast\Enum;

use MyCLabs\Enum\Enum;

/**
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 */
class ResponseCodes extends Enum
{
    const SUCCESSFUL_REQUEST = 0;
    const INITIALIZING = 1;
    const INTERNAL_ERROR = 2;
    const INVALID_REQUEST = 3;
    const INVALID_PARAMETER = 4;
    const GUARDED = 5;
    const TIME_OUT = 6;
    const FIRMWARE_UPDATING = 99;
    const ACCESS_ERROR = 100;
    const OTHER_ERRORS = 101;
    const WRONG_USER_NAME = 102;
    const WRONG_PASSWORD = 103;
    const ACCOUNT_EXPIRED = 104;
    const ACCOUNT_DISCONNECTED = 105;
    const ACCOUNT_NUMBER_REACHED_LIMIT = 106;
    const SERVER_MAINTENANCE = 107;
    const INVALID_ACCOUNT = 108;
    const LICENSE_ERROR = 109;
    const READ_ONLY_MODE = 110;
    const MAX_STATIONS = 111;
    const ACCESS_DENIED = 112;

    /**
     * @param $code
     *
     * @return string
     */
    public static function getMessage($code)
    {
        switch ($code) {
            case self::SUCCESSFUL_REQUEST:
                return 'Successful request';
            case self::INITIALIZING:
                return 'Initializing';
            case self::INTERNAL_ERROR:
                return 'Internal Error';
            case self::INVALID_REQUEST:
                return 'Invalid Request (A method did not exist, a method wasn’t appropriate etc.)';
            case self::INVALID_PARAMETER:
                return 'Invalid Parameter (Out of range, invalid characters etc.)';
            case self::GUARDED:
                return 'Guarded (Unable to setup in current status etc.)';
            case self::TIME_OUT:
                return 'Time Out';
            case self::FIRMWARE_UPDATING:
                return 'Firmware Updating';
            case self::ACCESS_ERROR:
                return 'Access Error';
            case self::OTHER_ERRORS:
                return 'Other Errors';
            case self::WRONG_USER_NAME:
                return 'Wrong User Nam';
            case self::WRONG_PASSWORD:
                return 'Wrong Password';
            case self::ACCOUNT_EXPIRED:
                return 'Account Expired';
            case self::ACCOUNT_DISCONNECTED:
                return 'Account Disconnected/Gone Off/Shut Down';
            case self::ACCOUNT_NUMBER_REACHED_LIMIT:
                return 'Account Number Reached to the Limit';
            case self::SERVER_MAINTENANCE:
                return 'Server Maintenance';
            case self::INVALID_ACCOUNT:
                return 'Invalid Account';
            case self::LICENSE_ERROR:
                return 'License Error';
            case self::READ_ONLY_MODE:
                return 'Read Only Mode';
            case self::MAX_STATIONS:
                return 'Max Stations';
            case self::ACCESS_DENIED:
                return 'Access Denied';

            default:
                return 'Unknown error';
        }
    }
}
