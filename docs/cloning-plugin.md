# Steps for Cloning Existing Plugin

After you have clone your project from Github to your local computer, run the following commands in your project root folder.

## Step 1: Install project packages

```bash
composer install
composer bob afl:bob:install-wpcli
composer bob afl:bob:require-dev-global
```

## Step 2: Run build

Run the build command for the first time and each time you add or remove files in the `src` folder.

```bash
composer build
```
