# To release a new plugin version

Use semantic versioning `X.Y.Z` where:
- `X` = major changes (breaking changes)
- `Y` = minor changes (new features, backward compatible)
- `Z` = patches (bug fixes, backward compatible)

1. On your Github.com Repo, create a new Github issue for the release.
2. Name the issue "Release Version X.Y.Z".
   - On the sidebar > Development > Create a branch from the main branch.
   - Then checkout branch to Github Desktop.
3. Launch your VS Code.

## Bump the version in the main plugin file 

In the plugin header comment (afl-plugin-boilerplate.php), increase the following version number.

`Requires PHP` is the minimum PHP version that this plugin will work with. Use X.Y only.
`Requires at least` is the minimum WordPress version that this plugin will work with. Use X.Y only.
`Tested up to` is the WordPress version that you have tested this plugin with. Use X.Y only.
`Version` is the plugin version which you need to bump for each release. Use X.Y.Z.

```php
/**
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * Tested up to:      6.9
 * Version:           0.0.1
 */
```

## Run composer command

Running the composer command below will build neccessary files, automatically update the plugin version and generate the language pot file.

- afl-plugin-boilerplate.php (main plugin file)
- readme.txt
- /language/afl-plugin-boilerplate.pot

```bash
composer afl:release
```

## Commit, Push, Merge

1. On Github Desktop, commit the file changes.
2. Preview Pull Request.
3. Create Pull Request.
4. On Github.com Repo, Merge Pull request.
5. Delete branch.

## Create a Tag

1. On Github Desktop, checkout the `main` branch.
2. Refresh and pull new changes.
3. Under the History tab, right click on the latest commit and `Create a Tag`.
4. Push the changes.

## Github Action

1. On Github.com Repo > Actions.
2. Select the latest job `Create Plugin Zip on Tag`.
3. Download the plugin zip file.
4. Run your testing on the zip file.

## Create a new Github Releese

1. On Github.com Repo > Releases.
2. Create a new release.
3. Select the latest tag.
4. Generate release notes.
5. Set as the latest release.
6. Wait for the `Create Plugin Zip on Release` action to be completed.
7. Refresh the release page.
8. The plugin zip file will be attached to the release.
