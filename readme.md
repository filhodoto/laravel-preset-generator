
# Laravel Preset Generator

Laravel preset that runs all presets needed for a project.

## Installation

Add this repository to the repositories in your project's composer.json file.

```
{
    ...
    "repositories": [
        {
            "type": "git",
            "url": "git@bitbucket.org:codedazur/laravel-presets.git"
        }
    ],
	...
}
```

Then require the preset using composer.

```
composer require codedazur/laravel-presets

```

## Usage

### 1. Run the Preset

```
php artisan preset codedazur

```

This will run a command that will ask which presets we want to install and then: 
1. Add them to _composer.json_
2. Require them
3. Run `php artisan preset` command for each one

## Add preset option

For now the preset options are defined in and array in **Preset.php**. To add another preset to the array the structure is as follow:

```
[
    'name' => 'Webpack', // How the command should refer to this preset
    'repository' => 'git@bitbucket.org:codedazur/laravel-preset-webpack.git', // Path to preset repository
    'require' => 'composer require codedazur/laravel-preset-webpack:dev-develop', // Full require command for preset
    'preset' => 'webpack', // Laravel command name e.g: "php artisan preset webpack' 
}
```