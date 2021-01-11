<?php
namespace App\Tools;

use DirectoryIterator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AngularCli extends AbstractExtension
{
    /**
     * @var array
     */
    protected static $ngFiles = [];

    public function getFunctions()
    {
        return [
            new TwigFunction('ngFiles', function (string $kernelProjectDir, string $project = 'frontend'): array {
                return static::getNgBuildFiles($kernelProjectDir, $project);
            }),
        ];
    }

    /**
     * @param string $kernelProjectDir
     * @param string $project
     * @return array
     */
    public static function getNgBuildFiles(string $kernelProjectDir, string $project = 'frontend'): array
    {
        if (array_key_exists($project, static::$ngFiles)) {
            return static::$ngFiles[$project];
        }

        // Quick'n a bit dirty solution to load correct ng files (until now, prod build with version string in name cannot be loaded)
        $ngFiles = [
            'js' => [ // take care at the order it's very important !!!
                'runtime' => 'runtime.js',
                'polyfills' => 'polyfills.js',
                'vendor' => 'vendor.js',
                'main' => 'main.js',
            ],
            'css' => [
                'styles' => 'styles.css',
            ]
        ];
        $json = file_get_contents($kernelProjectDir . '/assets/js/' . $project . '/angular.json');
        $jsonDecoded = json_decode($json, true);
        $path = $jsonDecoded['projects'][$project]['architect']['build']['options']['outputPath'];
        $pathParts = explode('/', $path);
        $ngBuildDir = [];
        $startBuildBir = false;
        foreach ($pathParts as $oneDir) {
            if ($oneDir === 'public') {
                $startBuildBir = true;
            }

            if (!$startBuildBir) {
                continue;
            }

            $ngBuildDir[] = $oneDir;
        }

        // find build files
        $ngBuildDirFromProject = '/' . implode('/', $ngBuildDir);
        $ngBuildDirFromPublic = '/' . implode('/', array_slice($ngBuildDir, 1)) . '/';
        $dirIt = new DirectoryIterator($kernelProjectDir . $ngBuildDirFromProject);
        foreach ($dirIt as $iteration) {
            if ($iteration->isFile()) {
                // improve this using array_* functions
                foreach ($ngFiles['js'] as $jsKey => $jsNgFile) {
                    // prevent source map files
                    if ($iteration->getExtension() !== 'js') {
                        continue;
                    }

                    if (strpos($iteration->getBasename(), $jsKey) !== false) {
                        $ngFiles['js'][$jsKey] = $ngBuildDirFromPublic.$iteration->getBasename();
                        continue;
                    }
                }

                foreach ($ngFiles['css'] as $cssKey => $cssNgFile) {
                    if (strpos($iteration->getBasename(), $cssKey) !== false) {
                        $ngFiles['css'][$cssKey] = $ngBuildDirFromPublic.$iteration->getBasename();
                        continue;
                    }
                }
            }
        }

        static::$ngFiles[$project] = $ngFiles;

        return static::$ngFiles[$project];
    }
}
