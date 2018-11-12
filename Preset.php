<?php

namespace PresetLocalGenerate;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\Presets\Preset as BasePreset;

class Preset extends BasePreset
{
    /**
     * @var Command
     */
    protected static $command;

    /**
     * Array with preset options
     */
    protected static $presets = [
        [
            'name' => 'Webpack',
            'repository' => 'git@bitbucket.org:codedazur/laravel-preset-webpack.git',
            'require' => 'composer require codedazur/laravel-preset-webpack:dev-develop',
            'preset' => 'webpack',
        ],
        [
            'name' => 'Vue',
            'repository' => 'git@bitbucket.org:codedazur/laravel-preset-vue.git',
            'require' => 'composer require codedazur/laravel-preset-vue:dev-develop',
            'preset' => 'vue-codedazur',
        ],
    ];

    /**
     * Array with selected presets
     */
    protected static $selectedPresets = [];


    public static function install(Command $command)
    {
        static::$command = $command;
        $presets = static::$presets;

        try {

            // Ask if we want to install presets
            foreach ($presets as $key => $preset) {
                if($command->confirm("Do you want to run " . $preset['name'] . " preset?")) {
                    // If yes, we add preset values to empty array where will put only the oes we want to install
                    array_push(static::$selectedPresets, $preset);
                }
            }

            // If there are presets to install
            if (count(static::$selectedPresets)) {
                // 1. Add repositories to composer.json
                static::updateRepository();

                // 2. Add repositories to composer.json
                static::requirePresets();

                // 3. Run the cargo preset
                static::runPresets();
            }
        } catch (Exception $exception) {
            static::error($exception->getMessage());
            exit (1);
        }

        $command->info("\nGenerator preset finished running");
    }


    /**
     * Update Scripts in composer.json
     *
     */
    protected static function updateRepository()
    {
        $selectedPresets = static::$selectedPresets;

        // Decode composer.json so we can access values as arrays
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        // Define which array in composer.json we want to look through
        $configurationKey = 'repositories';

        // Check if repositories exist, if not create it
        if( !isset($packages[$configurationKey]) ) {
            $packages[$configurationKey]  = [];
        }

        // Save existent repositories in array
        $currentRepositories = [];
        foreach ($packages[$configurationKey] as $repository) {
            array_push($currentRepositories, $repository['url']);
        }


        // For each preset we want to install
        foreach ($selectedPresets as $key => $preset) {

            // Check if repository already exists
            $hasRepository = array_filter($currentRepositories, function ($repository) use ($preset) {
                return $repository === $preset['repository'];
            });

            // If repository already exists, do nothing
            if ($hasRepository) {
                return;
            }

            // Get number of repositories
            $count = count($packages[$configurationKey]);

            // Add repository to repositories last position (using repositories number)
            $packages[$configurationKey][$count] = [
                "type"=> "git",
                "url"=> $preset['repository']
            ];
        }

        // Put composer.json back together
        file_put_contents(
            base_path('composer.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Run require scripts for each preset
     *
     */
    protected static function requirePresets()
    {
        $selectedPresets = static::$selectedPresets;
        foreach ($selectedPresets as $preset) {
            static::$command->warn('Require '. $preset['name'] .' Preset');
            exec($preset['require']);
        }
    }

    /**
     * Run require scripts for each preset
     *
     */
    protected static function runPresets()
    {
        $selectedPresets = static::$selectedPresets;
        foreach ($selectedPresets as $preset) {
            static::$command->warn('Run '. $preset['name'] .' Preset');
            static::$command->call('preset', ['type' => $preset['preset']]);
        }
    }
}