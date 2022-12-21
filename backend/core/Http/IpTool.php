<?php

/**
 * Provides IP related functionality
 */

namespace BitApps\FM\Core\Http;

trait IpTool
{
    /**
     * Check ip address
     *
     * @return ip_addr IP address of current visitor
     */
    private static function _checkIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * Check device info
     *
     * @return void
     */
    private static function _checkDevice()
    {
        if (isset($_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if (isset($HTTP_SERVER_VARS)) {
                $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } else {
                global $HTTP_USER_AGENT;
                $user_agent = $HTTP_USER_AGENT;
            }
        }

        return self::_getBrowserName($user_agent).'|'.self::_getOS($user_agent);
    }

    /**
     * Get browser name
     *
     * @link https://stackoverflow.com/questions/18070154/get-operating-system-info
     *
     * @param  string  $user_agent $_SERVER['HTTP_USER_AGENT']
     * @return void
     */
    private static function _getBrowserName($user_agent)
    {
        // Make case insensitive.
        $t = strtolower($user_agent);

        // If the string *starts* with the string, strpos returns 0 (i.e., FALSE). Do a ghetto hack and start with a space.
        // "[strpos()] may return Boolean FALSE, but may also return a non-Boolean value which evaluates to FALSE."
        //     http://php.net/manual/en/function.strpos.php
        $t = ' '.$t;

        // Humans / Regular Users
        if (strpos($t, 'opera') || strpos($t, 'opr/')) {
            return 'Opera';
        } elseif (strpos($t, 'edge')) {
            return 'Edge';
        } elseif (strpos($t, 'Edg')) {
            return 'Edge';
        } elseif (strpos($t, 'chrome')) {
            return 'Chrome';
        } elseif (strpos($t, 'safari')) {
            return 'Safari';
        } elseif (strpos($t, 'firefox')) {
            return 'Firefox';
        } elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) {
            return 'Internet Explorer';
        } elseif (strpos($t, 'google')) {
            return 'Googlebot';
        } elseif (strpos($t, 'bing')) {
            return 'Bingbot';
        } elseif (strpos($t, 'slurp')) {
            return 'Yahoo! Slurp';
        } elseif (strpos($t, 'duckduckgo')) {
            return 'DuckDuckBot';
        } elseif (strpos($t, 'baidu')) {
            return 'Baidu';
        } elseif (strpos($t, 'yandex')) {
            return 'Yandex';
        } elseif (strpos($t, 'sogou')) {
            return 'Sogou';
        } elseif (strpos($t, 'exabot')) {
            return 'Exabot';
        } elseif (strpos($t, 'msn')) {
            return 'MSN';
        }

        // Common Tools and Bots
        elseif (strpos($t, 'mj12bot')) {
            return 'Majestic';
        } elseif (strpos($t, 'ahrefs')) {
            return 'Ahrefs';
        } elseif (strpos($t, 'semrush')) {
            return 'SEMRush';
        } elseif (strpos($t, 'rogerbot') || strpos($t, 'dotbot')) {
            return 'Moz';
        } elseif (strpos($t, 'frog') || strpos($t, 'screaming')) {
            return 'Screaming Frog';
        } elseif (strpos($t, 'facebook')) {
            return 'Facebook';
        } elseif (strpos($t, 'pinterest')) {
            return 'Pinterest';
        } elseif (
            strpos($t, 'crawler') || strpos($t, 'api')
            || strpos($t, 'spider') || strpos($t, 'http')
            || strpos($t, 'bot') || strpos($t, 'archive')
            || strpos($t, 'info') || strpos($t, 'data')
        ) {
            return 'Bot';
        }

        return 'Other (Unknown)';
    }

    /**
     * Provide Operating System Information of User
     *
     * @link https://stackoverflow.com/questions/18070154/get-operating-system-info
     *
     * @return void
     */
    private static function _getOS($user_agent)
    {
        $ros[] = ['Windows XP', 'Windows XP'];
        $ros[] = ['Windows NT 5.1|Windows NT5.1', 'Windows XP'];
        $ros[] = ['Windows 2000', 'Windows 2000'];
        $ros[] = ['Windows NT 5.0', 'Windows 2000'];
        $ros[] = ['Windows NT 4.0|WinNT4.0', 'Windows NT'];
        $ros[] = ['Windows NT 5.2', 'Windows Server 2003'];
        $ros[] = ['Windows NT 6.0', 'Windows Vista'];
        $ros[] = ['Windows NT 7.0', 'Windows 7'];
        $ros[] = ['Windows CE', 'Windows CE'];
        $ros[] = [
            '(media center pc).([0-9]{1,2}\.[0-9]{1,2})',
            'Windows Media Center',
        ];
        $ros[] = ['(win)([0-9]{1,2}\.[0-9x]{1,2})', 'Windows'];
        $ros[] = ['(win)([0-9]{2})', 'Windows'];
        $ros[] = ['(windows)([0-9x]{2})', 'Windows'];
        // Doesn't seem like these are necessary...not totally sure though..
        //$ros[] = array('(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'Windows NT');
        //$ros[] = array('(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})', 'Windows NT'); // fix by bg
        $ros[] = ['Windows ME', 'Windows ME'];
        $ros[] = ['Win 9x 4.90', 'Windows ME'];
        $ros[] = ['Windows 98|Win98', 'Windows 98'];
        $ros[] = ['Windows 95', 'Windows 95'];
        $ros[] = ['(windows)([0-9]{1,2}\.[0-9]{1,2})', 'Windows'];
        $ros[] = ['win32', 'Windows'];
        $ros[] = ['(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})', 'Java'];
        $ros[] = ['(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}', 'Solaris'];
        $ros[] = ['dos x86', 'DOS'];
        $ros[] = ['unix', 'Unix'];
        //Android
        $ros[] = ['SM', 'Samsung'];
        $ros[] = ['HTC', 'HTC'];
        $ros[] = ['LG', 'LG'];
        $ros[] = ['Microsoft', 'Microsoft'];
        $ros[] = ['Pixel', 'Pixel'];
        $ros[] = ['MI', 'Xiaomi'];
        $ros[] = ['Xiaomi', 'Xiaomi'];
        $ros[] = ['Android', 'Android'];
        $ros[] = ['android', 'Android'];

        //iPhone
        $ros[] = ['iPhone', 'iPhone'];

        $ros[] = ['Mac OS X', 'Mac OS X'];
        $ros[] = ['Mac OS X Puma', 'Mac OS X 10.1[^0-9]'];
        $ros[] = ['Mac_PowerPC', 'Macintosh PowerPC'];
        $ros[] = ['(mac|Macintosh)', 'Mac OS'];
        $ros[] = ['(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'SunOS'];
        $ros[] = ['(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}', 'BeOS'];
        $ros[] = ['(risc os)([0-9]{1,2}\.[0-9]{1,2})', 'RISC OS'];
        $ros[] = ['os\/2', 'OS/2'];
        $ros[] = ['freebsd', 'FreeBSD'];
        $ros[] = ['openbsd', 'OpenBSD'];
        $ros[] = ['netbsd', 'NetBSD'];
        $ros[] = ['irix', 'IRIX'];
        $ros[] = ['plan9', 'Plan9'];
        $ros[] = ['osf', 'OSF'];
        $ros[] = ['aix', 'AIX'];
        $ros[] = ['GNU Hurd', 'GNU Hurd'];
        $ros[] = ['(fedora)', 'Linux - Fedora'];
        $ros[] = ['(kubuntu)', 'Linux - Kubuntu'];
        $ros[] = ['(ubuntu)', 'Linux - Ubuntu'];
        $ros[] = ['(debian)', 'Linux - Debian'];
        $ros[] = ['(CentOS)', 'Linux - CentOS'];
        $ros[] = [
            '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)',
            'Linux - Mandriva',
        ];
        $ros[] = [
            '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)',
            'Linux - SUSE',
        ];
        $ros[] = ['(Dropline)', 'Linux - Slackware (Dropline GNOME)'];
        $ros[] = ['(ASPLinux)', 'Linux - ASPLinux'];
        $ros[] = ['(Red Hat)', 'Linux - Red Hat'];
        // Loads of Linux machines will be detected as unix.
        // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
        //$ros[] = array('X11', 'Unix');
        $ros[] = ['(linux)', 'Linux'];
        $ros[] = ['(amigaos)([0-9]{1,2}\.[0-9]{1,2})', 'AmigaOS'];
        $ros[] = ['amiga-aweb', 'AmigaOS'];
        $ros[] = ['amiga', 'Amiga'];
        $ros[] = ['AvantGo', 'PalmOS'];
        //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}', 'Linux');
        //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}', 'Linux');
        //$ros[] = array('(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})', 'Linux');
        $ros[] = ['[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})', 'Linux'];
        $ros[] = ['(webtv)/([0-9]{1,2}\.[0-9]{1,2})', 'WebTV'];
        $ros[] = ['Dreamcast', 'Dreamcast OS'];
        $ros[] = ['GetRight', 'Windows'];
        $ros[] = ['go!zilla', 'Windows'];
        $ros[] = ['gozilla', 'Windows'];
        $ros[] = ['gulliver', 'Windows'];
        $ros[] = ['ia archiver', 'Windows'];
        $ros[] = ['NetPositive', 'Windows'];
        $ros[] = ['mass downloader', 'Windows'];
        $ros[] = ['microsoft', 'Windows'];
        $ros[] = ['offline explorer', 'Windows'];
        $ros[] = ['teleport', 'Windows'];
        $ros[] = ['web downloader', 'Windows'];
        $ros[] = ['webcapture', 'Windows'];
        $ros[] = ['webcollage', 'Windows'];
        $ros[] = ['webcopier', 'Windows'];
        $ros[] = ['webstripper', 'Windows'];
        $ros[] = ['webzip', 'Windows'];
        $ros[] = ['wget', 'Windows'];
        $ros[] = ['Java', 'Unknown'];
        $ros[] = ['flashget', 'Windows'];
        // delete next line if the script show not the right OS
        //$ros[] = array('(PHP)/([0-9]{1,2}.[0-9]{1,2})', 'PHP');
        $ros[] = ['MS FrontPage', 'Windows'];
        $ros[] = ['(msproxy)/([0-9]{1,2}.[0-9]{1,2})', 'Windows'];
        $ros[] = ['(msie)([0-9]{1,2}.[0-9]{1,2})', 'Windows'];
        $ros[] = ['libwww-perl', 'Unix'];
        $ros[] = ['UP.Browser', 'Windows CE'];
        $ros[] = ['NetAnts', 'Windows'];
        $ros[] = ['Android', 'Android'];
        $file = count($ros);
        $os = '';
        for ($n = 0; $n < $file; $n++) {
            if (@preg_match('/'.$ros[$n][0].'/i', $user_agent)) {
                $os = @$ros[$n][1];
                break;
            }
        }

        return trim($os);
    }

    /**
     * Set user details ip,cdevice, user_id, user's visited page, current mysql formatted time
     *
     * @return array of user details
     */
    private static function _setUserDetail()
    {
        $user_details['ip'] = ip2long(self::_checkIP());
        $user_details['device'] = self::_checkDevice();
        $user_details['id'] = get_current_user_id();
        $user_details['page'] = is_object(get_post()) ? get_permalink(get_post()->ID) : null;
        $user_details['time'] = current_time('mysql');

        return $user_details;
    }

    /**
     * Provide user details
     *
     * @return _setUserDetail user details array
     */
    public static function getUserDetail()
    {
        return self::_setUserDetail();
    }

    /**
     * Provide user IP address
     *
     * @return ip
     */
    public static function getIP()
    {
        return self::_checkIP();
    }
}
