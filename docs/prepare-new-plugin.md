# Steps to Prepare for a new WordPress Plugin

Run the following commands in your terminal from the root folder of your WordPress plugin.

## Step 1: Install project packages

```bash
composer install
```

## Step 2: Begin the new plugin process

This will prepare the project folder for a new plugin.

```bash
composer bob afl:bob:new-plugin
```

## Step 3: Edit the .afl-extra/config/plugin-renamer-config.php

A plugin renamer config (plugin-renamer-config.php) file will be copied over to your plugin `.afl-extra/config` folder.

Edit the configurations in the config file to name your new plugin.

## Step 4: Run the plugin renamer tool

- The plugin renamer command will use the config file above to rename the plugin files.
- Then run composer update to update the composer.lock file.

```bash
composer bob afl:bob:plugin-renamer
composer update
```

## Step 5: Run build

Run the build command for the first time and each time you add or remove files in the `src` folder.

```bash
composer build
```

If you had add / remove a new composer dependency package, run the command:

```bash
composer update
```

## Step 5: Start Coding

- You can now start coding in the src folder.
- Register new modules in the plugin's `config/provider.php` file.

Run this command each time you add or remove files in the `src` folder.

```bash
composer build
```

## Step 6: Preparing a new Plugin Release Version

Follow the steps in [release.md](release.md) file.
