# Steps to Prepare for a new WordPress Plugin

Run the following commands in your terminal from the root folder of your WordPress plugin.

## Step 1: Run the first time command

A plugin renamer config (plugin-renamer-config.php) file will be copied over to your plugin `.afl-extra/config` folder.

```bash
composer bob afl:bob:new-plugin
```

## Step 2: Edit the .afl-extra/config/plugin-renamer-config.php

To rename your plugin, rename the contents in the `.afl-extra/config/plugin-renamer-config.php` file then run the commands below in the terminal.

## Step 3: Run the plugin renamer tool

```bash
composer bob afl:bob:plugin-rename
```

## Step 4: Install first time dependencies

Install production and dev packages:

```bash
composer install
```

## Step 5: Run build

Run the build command for the first time and each time you add or remove files in the `src` folder.

```bash
composer afl:build
```

If you had add / remove a new composer dependency package, run the command:

```bash
composer update
```

## Step 8: Start Coding

- You can now start coding in the src folder.
- Register new modules in the plugin's `config/provider.php` file.

Run this command each time you add or remove files in the `src` folder.

```bash
composer afl:build
```

## Step 9: Preparing a new Plugin Release Version

Follow the steps in [release.md](release.md) file.
