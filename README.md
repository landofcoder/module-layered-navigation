# Mage2 Module Lof LayeredNavigation

    ``landofcoder/module-layered-navigation``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_LayeredNavigation`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require lof/module-layered-navigation`
 - enable the module by running `php bin/magento module:enable Lof_LayeredNavigation`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

## Configuration
1. Create layered-navigation account

2. Get layered-navigation api

3. Create subscribe contacts list on layered-navigation

4. Create unsubscript contacts list on layered-navigation

5. Config on module

 - Enabled (layered-navigation/general/enabled)

 - API Key (layered-navigation/general/api_key)

 - Subscribe List (layered-navigation/general/subscribe_list)

 - Unsubscribe List	 (layered-navigation/general/unsubscribe_list)
 
 - Other List	 (layered-navigation/general/other_list)

 - Add customers without subscriptions status in layered-navigation	 (layered-navigation/general/add_customer)

 - webhook_url (layered-navigation/general/webhook_url)

 - Webhook Url	 (layered-navigation/general/list_for_new_customer)

 - Cron Enabled (layered-navigation/sync/cron_enable)

## Specifications




## Attributes



