<?php

/** @noinspection HtmlUnknownTarget */

return [

    'frontman' => [
        'version'  => env('FRONTMAN_VERSION'),
        'base_url' => 'https://github.com/cloudradar-monitoring/frontman/releases/download/:VERSION/frontman_:VERSION',
        'hub_url'  => env('HUB_URL').'/checks/',
        'conf'     => [
            [
                'key'      => 'windows_via_shell',
                'display'  => 'Microsoft Windows 64bit (via powershell)',
                'versions' => 'All Windows versions starting from Windows 7 or Server 2012',
                'text'     => 'Copy the code to a powershell terminal which has been started with administrative rights',
                'code'     => '[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$url=":BASE_URL_Windows_x86_64.msi"
Invoke-WebRequest -Uri $url -OutFile "frontman_:VERSION_Windows_x86_64.msi"
msiexec /i frontman_:VERSION_Windows_x86_64.msi HUB_URL=":HUB_URL" HUB_USER=":FRONTMAN_ID" HUB_PASSWORD=":PASSWORD"',
            ],
            [
                'key'      => 'windows_via_browser',
                'display'  => 'Microsoft Windows 64bit (via browser download)',
                'versions' => 'All Windows versions starting from Windows 7 or Server 2012',
                'text'     => '<a href=":BASE_URL_Windows_x86_64.msi" target="_blank">Download frontman_:VERSION_Windows_x86_64.msi</a> and install it. Use the following information when prompted during the installation.',
                'code'     => 'hub_url=:HUB_URL
hub_user=:FRONTMAN_ID
hub_password=:PASSWORD
',
            ],
            [
                'key'      => 'linux_ubuntu',
                'display'  => 'Debian/Ubuntu Linux 64bit',
                'versions' => 'Ubuntu starting from 14.04 and Debian starting from 8',
                'text'     => 'Copy the commands to a terminal and execute. Root or sudo privileges are needed.',
                'code'     => 'curl -L -O :BASE_URL_linux_amd64.deb
sudo \\
FRONTMAN_HUB_URL=:HUB_URL \\
FRONTMAN_HUB_USER=:FRONTMAN_ID \\
FRONTMAN_HUB_PASSWORD=:PASSWORD \\
dpkg -i frontman_:VERSION_linux_amd64.deb
',
            ],
            [
                'key'      => 'linux_centos',
                'display'  => 'RedHat/CentOS Linux 64 bit',
                'versions' => 'RedHat and CentOS starting from 6',
                'text'     => 'Copy the commands to a terminal and execute. Root or sudo privileges are needed.',
                'code'     => 'curl -L -O :BASE_URL_linux_amd64.rpm
sudo \\
FRONTMAN_HUB_URL=:HUB_URL \\
FRONTMAN_HUB_USER=:FRONTMAN_ID \\
FRONTMAN_HUB_PASSWORD=:PASSWORD \\
rpm -i frontman_:VERSION_linux_amd64.rpm
',
            ],
            [
                'key'      => 'other_manual',
                'display'  => 'other (manual installation)',
                'versions' => 'For a manual installation refer to our <a href="https://docs.cloudradar.io/managing-frontman/installing-a-frontman" target="_blank">knowledge base</a> where you will find downloads for almost every operating system.',
                'text'     => 'Copy the following lines to your frontman.conf, if you create the configuration file manually.',
                'code'     => 'io_mode = "http"
hub_url = ":HUB_URL"
hub_user = ":FRONTMAN_ID"
hub_password = ":PASSWORD"',
            ],
        ],
    ],

    'cagent' => [
        'version'  => env('CAGENT_VERSION'),
        'base_url' => 'https://github.com/cloudradar-monitoring/cagent/releases/download/:VERSION/cagent_:VERSION',
        'win_url' => 'https://repo.cloudradar.io/windows/cagent/cagent-plus_:VERSION',
        'hub_url'  => env('HUB_URL').'/ctrapper/',
        'conf' => [
            [
                'key'      => 'windows_via_shell',
                'display'  => 'Microsoft Windows 64bit (via powershell)',
                'versions' => 'All Windows versions starting from Windows 7 or Server 2012',
                'text'     => 'Copy the code to a powershell terminal which has been started with administrative rights',
                'code' => '[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$url=":WIN_URL_Windows_64.msi"
Invoke-WebRequest -Uri $url -OutFile "cagent-plus_:VERSION_Windows_64.msi"
msiexec /i cagent-plus_:VERSION_Windows_64.msi HUB_URL=":HUB_URL" HUB_USER=":HOST_ID" HUB_PASSWORD=":PASSWORD"',
            ],
            [
                'key'      => 'windows_via_browser',
                'display'  => 'Microsoft Windows 64bit (via browser download)',
                'versions' => 'All Windows versions starting from Windows 7 or Server 2012',
                'text'     => '<a href=":WIN_URL_Windows_64.msi" target="_blank">Download Cagent Plus :VERSION for Windows_64 </a> and install it. Use the following information when prompted during the installation.',
                'code'     => 'hub_url=:HUB_URL
hub_user=:HOST_ID
hub_password=:PASSWORD
',
            ],
            [
                'key'      => 'linux_ubuntu',
                'display'  => 'Debian/Ubuntu Linux 64bit',
                'versions' => 'Ubuntu starting from 14.04 and Debian starting from 8',
                'text'     => 'Copy the commands to a terminal and execute. Root or sudo privileges are needed.',
                'code'     => 'curl -O https://repo.cloudradar.io/pool/utils/c/cloudradar-release/cloudradar-release.deb
sudo dpkg -i cloudradar-release.deb
sudo apt-get update
sudo \\
CAGENT_HUB_URL=:HUB_URL \\
CAGENT_HUB_USER=:HOST_ID \\
CAGENT_HUB_PASSWORD=:PASSWORD \\
apt-get install cagent
',
            ],
            [
                'key'      => 'linux_centos',
                'display'  => 'RedHat/CentOS Linux 64 bit',
                'versions' => 'RedHat and CentOS starting from 6',
                'text'     => 'Copy the commands to a terminal and execute. Root or sudo privileges are needed.',
                'code'     => ' sudo rpm -i https://repo.cloudradar.io/cloudradar-1.0.0-1.el7.noarch.rpm
sudo \\
CAGENT_HUB_URL=:HUB_URL \\
CAGENT_HUB_USER=:HOST_ID \\
CAGENT_HUB_PASSWORD=:PASSWORD \\
yum install cagent
',
            ],
            [
                'key'      => 'other_manual',
                'display'  => 'other (manual installation)',
                'versions' => 'For a manual installation refer to our <a href="https://docs.cloudradar.io/configuring-hosts/installing-agents" target="_blank">knowledge base</a> where you will find downloads for almost every operating system.',
                'text'     => 'Copy the following lines to your cagent.conf, if you create the configuration file manually.',
                'code'     => 'hub_url = ":HUB_URL"
hub_user = ":HOST_ID"
hub_password = ":PASSWORD"',
            ],
        ],
    ],

];
