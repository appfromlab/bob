# Github

## Enviroment Variables

You will have access to these variables in your GitHub Actions workflow using the `env` context.

For example, you can reference them as `${{ env.AFL_PLUGIN_FOLDER_NAME }}` in your workflow file.

Environment variables are set after `composer build -- --github-setup-env` command is executed.

| Name                       | Value                            | Description                                    |
|----------------------------|----------------------------------|------------------------------------------------|
| AFL_PLUGIN_FOLDER_NAME     | your-plugin-slug                 | The folder name of your plugin                 |
| AFL_PLUGIN_VERSION_NUMBER  | 1.0.0                            | The version number of your plugin              |
| AFL_PLUGIN_DIST_DIR        | /home/../.afl-dist/<plugin_name> | The distribution folder path of your plugin    |
