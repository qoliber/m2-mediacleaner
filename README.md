## Media Cleaner

A Magento module for cleaning up media files.


### Installation

Installation is via `composer`
```
composer require qoliber/m2-mediacleaner
```

After installing the package, run the following command:
```
php bin/magento setup:upgrade
```

### Usage

To use this module, simply enable it in the Magento configuration (`Qoliber > Media Cleaner`) and set the desired cleaning options. The module will then run automatically according to the configured cron expression.

#### Cleaning command

> php bin/magento qoliber:media:clean

#### Dry run mode

If you want to test the cleaning process without actually deleting the files, you can set the `--dry-run` option in the command:
> php bin/magento qoliber:media:clean --dry-run


### Adding cleaners

All cleaner classes must implement the `Qoliber\MediaCleaner\Api\Cleaner\CleanerInterface` interface.

You can add a custom cleaner by modifying `di.xml`:
```xml
    <type name="Qoliber\MediaCleaner\Api\CleanerProviderInterface">
        <arguments>
            <argument name="cleaners" xsi:type="array">
                <item name="custom_cleaner"
                      xsi:type="object">Foo\Bar\Cleaner\CustomCleaner</item>
            </argument>
        </arguments>
    </type>
```
