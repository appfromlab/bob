# Steps to Prepare for a new WordPress Plugin

## Step 1: Install Composer

- Visit [getcomposer.org](https://getcomposer.org/) and follow the installation instructions for your operating system.
- Note that composer commands have to be run in the terminal from the plugin root folder.
- Check that composer is installed successfully.

```bash
composer -V
```

## Step 2: Run the first time command

A plugin renamer config file will be copied over to the `.afl-extra/config` folder.

```bash
composer afl:first-time
```

## Step 3: Edit the .afl-extra/config/plugin-renamer-config.php

To rename your plugin, rename the contents in the `.afl-extra/config/plugin-renamer-config.php` file then run the commands below in the terminal.

## Step 4: Run the plugin renamer tool

```bash
composer afl:plugin-rename
```

## Step 5: Install first time dependencies

```bash
composer afl:install-global
```

```bash
composer afl:install-local-phar
```

Install production and dev packages:

```bash
composer install
```

## Step 6: Run build

Run this command each time you add or remove files in the `src` folder.

```bash
composer afl:build
```

If you had add / remove a new composer package, run the command:

```bash
composer update
composer afl:build
```

## Step 7: Read and understand the Sample Module

Once you have understand the module, delete the `Sample` module folder.

Then remove `Sample_Service_Provider` from [config/providers.php](../../config/providers.php) providers array:

```php
'providers' => array(
	//MyVendorName\AFL_Plugin_Boilerplate\Modules\Sample\Sample_Service_Provider::class
),
```

## Step 8: Start Coding

- You can now start coding in the src folder.
- Register new modules in the [config/provider.php](../../config/provider.php) file.

Run this command each time you add or remove files in the `src` folder.

```bash
composer afl:build
```

## Step 9: Preparing a new Plugin Release Version

Follow the steps in [release.md](release.md) file.
