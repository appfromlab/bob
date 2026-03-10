# Steps to Prepare for a new WordPress Plugin

Run the following commands in your terminal from the root folder of your WordPress plugin.

## Step 1: Install project packages

```bash
composer install
composer bin all install
```

## Step 2: Begin the new plugin process

This command will prepare the project folder for a new plugin.

```bash
composer bob afl:bob:new-plugin
```

## Step 3: Edit the .afl-extra/config/plugin-renamer-config.php

A plugin renamer config file will be copied over to your project `.afl-extra/config/plugin-renamer-config.php`.

Edit the configurations in the config file to name your new plugin.

## Step 4: Run the plugin renamer tool

The plugin renamer command will use the plugin renamer config file above to run the plugin renamer process.

```bash
composer bob afl:bob:plugin-renamer
```
