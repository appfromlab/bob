# appfromlab/bob

A composer plugin for automating WordPress plugin development tasks.

Run the following command in your terminal from the root folder of your WordPress plugin.

## Installation

You can skip this installation section because the plugin boilerplate already includes this package as a dev dependency.

First it is recommended to use [bamarni/composer-bin-plugin](https://github.com/bamarni/composer-bin-plugin) to isolate project-specific composer dev dependencies.

```bash
composer require --dev bamarni/composer-bin-plugin:"~1.9.0"
```

Then install this package as a dev dependency in the vendor-bin/appfromlab-bob folder.

```bash
composer bin appfromlab-bob require --dev appfromlab/bob:"<1.0.0"
```

## List of Commands

Add a script to your plugin's `composer.json` file to run Bob commands easily.

```json
"scripts": {
	"bob": "@composer bin appfromlab-bob --ansi",
	"afl:build": "composer bob afl:build",
	"afl:release": "composer bob afl:release",
}
```

You can now run Bob commands using:

```bash
composer bob <command>
```

## Available Commands

- **afl:bob:hello** - Say hello.
- **afl:bob:new-plugin** - Copy plugin-renamer-config.php and delete composer.lock file.
- **afl:bob:install-wpcli** - Install WP-CLI for WP commands.
- **afl:bob:require-dev-global** - Add global composer packages for development.
- **afl:bob:plugin-renamer-copy-config** - Copy plugin renamer file to your WordPress plugin folder.
- **afl:bob:plugin-renamer** - Rename a plugin using the plugin renamer config file.
- **afl:bob:build** - Perform build process.
- **afl:bob:scope** - Perform php-scoper on vendor folder.
- **afl:bob:phpcbf** - Beautify the plugin PHP files using PHPCBF.
- **afl:bob:phpcbf-vendor-prefixed** - Beautify only the vendor-prefixed composer folder.
- **afl:bob:delete-composer-lock** - Delete composer.lock file in the WordPress plugin folder.
- **afl:bob:delete-vendor-prefixed** - Delete vendor-prefixed folder in the WordPress plugin folder.
- **afl:bob:bump-version** - Bump plugin version using value from plugin header.
- **afl:bob:make-pot** - Generate the plugin language POT file.
- **afl:bob:generate-readme** - Generate plugin readme.txt based from individual files from the .afl-extra/readme folder.
- **afl:bob:release** - Perform the release process which builds the code, bump version, generate readme.txt and make-pot.
